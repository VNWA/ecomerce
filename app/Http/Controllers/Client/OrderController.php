<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\Stripe;
use App\Services\PayPal;
use Carbon\Carbon;
use Config;
use DB;
use Illuminate\Http\Request;
use Validator;
class OrderController extends Controller
{
    protected $stripe, $paypal;

    public function __construct(Stripe $stripe, Paypal $paypal)
    {
        $this->stripe = $stripe;
        $this->paypal = $paypal;
    }
    private function handleCheckoutSessionCompleted($event)
    {
        $session = $event->data->object;
        $orderCode = $session->metadata->order_code ?? null;
        $orderCode = trim($orderCode);
        $db = $session->metadata->db ?? null;
        if (!$orderCode || !$db) {
            \Log::error($session->metadata);
            \Log::error('Order code missing in checkout.session.completed event');
            return;
        } else {

            Config::set('database.default', $db);

            $order = Order::where('code', $orderCode)->first();
            if (!$order) {
                \Log::error('Order not found for code: ' . $orderCode);
                return;
            }

            if ($session->payment_status == 'paid') {
                $order->payment_status = 'completed';
                $order->save();
                \Log::info('Order ' . $orderCode . ' marked as completed.');
            }

        }
    }

    protected function handlePaymentIntentSucceeded($event)
    {
        $paymentIntent = $event->data->object;
        $orderCode = $paymentIntent->metadata->order_code ?? null;

        if (!$orderCode) {
            \Log::error('---');
            \Log::error($paymentIntent->metadata);
            \Log::error('---');
            \Log::error('Order code missing in payment_intent.succeeded event');
            return;
        } else {

            $order = Order::where('code', trim($orderCode))->first();
            if (!$order) {
                \Log::error('Order not found for code: ' . $orderCode);
                return;
            }

            $order->payment_status = 'completed';
            $order->save();
            \Log::info('Order ' . $orderCode . ' marked as completed.');
        }


    }
    public function stripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            $event = $this->stripe->webhookEvent($payload, $sig_header);
        } catch (\Throwable $th) {
            \Log::error('Stripe Webhook Error', ['message' => $th->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        try {
            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutSessionCompleted($event);
                    break;

                // case 'payment_intent.succeeded':
                //     $this->handlePaymentIntentSucceeded($event);
                //     break;
                default:
                    // \Log::info('Unhandled event type: ' . $event->type);
                    break;
            }
        } catch (\Throwable $th) {
            \Log::error('Stripe Webhook Processing Error', ['message' => $th->getMessage()]);
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }

        return response()->json(['status' => 'success']);
    }



    private function stripeCreatePaymentLink($origin, $orderCode, $orderPrice)
    {
        if (empty($origin) || empty($orderCode) || empty($orderPrice)) {
            throw new \InvalidArgumentException('Missing required parameters.');
        }

        return $this->stripe->paymentLink($orderPrice * 100, $orderCode, $origin, 'usd');
    }


    public function orderItems($code)
    {
        try {
            $order = Order::where('code', $code)->first();
            if ($order) {
                return response()->json([
                    'message' => 'success',
                    'items' => $order->items,
                    'logs' => $order->logs
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Data Not Found.'
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'post_code' => 'required|string|max:7',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'address_number' => 'required|string|max:255',
            'paymentMethod' => 'required|string',
            'delivery' => 'required|array',
            'coupon' => 'nullable|array',
            'cartItems' => 'required|array',
            'total' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Kiểm tra coupon nếu có
        $coupon = null;
        if (!empty($request->coupon) && $request->coupon['code']) {
            $coupon = Coupon::where('code', $request->coupon['code'])
                ->where('qnt', '>', 0) // Số lượng phải lớn hơn 0
                ->where(function ($query) {
                    $query->where('is_duration', 0)
                        ->orWhere(function ($query) {
                            $query->where('is_duration', 1)
                                ->where('start_time', '<=', Carbon::now())
                                ->where('end_time', '>=', Carbon::now());
                        });
                })
                ->exists();

            if (!$coupon) {
                return response()->json([
                    'message' => 'Coupon is not valid or has expired.'
                ], 400);
            }
        }

        // Tiến hành tạo đơn hàng nếu tất cả đều hợp lệ
        DB::beginTransaction();

        try {
            // Tạo đơn hàng
            $order = Order::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'post_code' => $request->post_code,
                'city' => $request->city,
                'address' => $request->address,
                'address_number' => $request->address_number,
                'note' => $request->note,
                'payment_method' => $request->paymentMethod,
                'delivery' => $request->delivery, // Lưu mảng delivery dưới dạng JSON
                'total' => $request->total,
            ]);

            // Áp dụng coupon nếu có
            if ($coupon) {
                $order->coupon = $request->coupon;
                $order->save();
            }

            // Lưu các mục trong đơn hàng
            foreach ($request->cartItems as $key => $item) {
                $product = Product::where('sku', $key)->firstOrFail();
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'image' => $product->images[0],
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'sku' => $product->sku,
                    'price' => $product->price_new,
                    'quantity' => $item['qnt'],

                ]);

            }

            // Commit transaction
            DB::commit();

            return response()->json([
                'message' => 'Order created successfully!',
                'orderCode' => $order->code,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function test($orderID = null)
    {

    }
    public function detail(Request $request, $code)
    {
        if ($code) {
            // Lấy dữ liệu với model Eloquent
            $order = Order::where('code', $code)
                // ->where('payment_status', 'pending')
                ->with(['items'])
                ->first();

            if ($order) {
                // Ẩn các trường nhạy cảm trong model chính
                $order->create_time = Carbon::parse($order->created_at)->format('H:i , d/m/Y ');
                $order->phoneFormat = $this->formatPhone($order->phone);
                $order->emailFormat = $this->formatEmail($order->email);

                $order->makeHidden(['id', 'coupon_id', 'phone', 'created_at', 'updated_at']);
                // Ẩn các trường nhạy cảm trong các quan hệ
                $order->items->each(function ($item) {
                    $item->makeHidden(['created_at', 'updated_at', 'id', 'order_id', 'product_id']);
                });
                $paymentUrl = '';
                if ($order->payment_method == 'stripe') {
                    $origin = $request->headers->get('Origin');
                    $origin = $origin . '/order/checkout?code=' . $order->code;
                    $paymentUrl = $this->stripeCreatePaymentLink($origin, $order->code, $order->total);
                }
                return response()->json([
                    'message' => 'Load Data Success',
                    'data' => $order,
                    'payment_url' => $paymentUrl
                ], 200);
            } else {
                return response()->json(['message' => 'Data Not Found'], 404);
            }
        } else {
            return response()->json(['message' => 'Data Not Found'], 404);
        }
    }
    // public function paymenRedirect($status, $orderCode)
    // {
    //     try {
    //         if ($status == 'success') {

    //             $order = Order::where('code', $orderCode)->where('payment_status', 'pending')->first();
    //             if ($order) {
    //                 $order->payment_status = 'completed';
    //                 $order->save();
    //                 return redirect(env('FRONTEND_URL', 'https://vinawebapp.com') . '/order/success');
    //             }

    //         }
    //         return redirect(env('FRONTEND_URL', 'https://vinawebapp.com') . '/order/cancel');
    //     } catch (\Throwable $th) {
    //         return redirect(env('FRONTEND_URL', 'https://vinawebapp.com') . '/order/cancel');

    //     }

    // }




    public function payment(Request $request, $code)
    {


        if ($code) {
            // Kiểm tra dữ liệu có trong yêu cầu
            if ($request->orderID && $request->payerID && $request->paymentID) {
                $order = Order::where('code', $code)->first();

                if ($order) {
                    $orderID = $request->orderID;
                    $paymentId = $request->paymentID; // Lưu ý tham số là paymentID
                    $payerId = $request->payerID; // Lưu ý tham số là payerID
                    $amount = $order->total;
                    if ($this->paypal->verifyPayment($code)) {
                        $order->payment_status = 'completed';
                        $order->save();
                        return response()->json(['message' => 'Payment completed successfully'], 200);
                    } else {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Payment verification failed'
                        ], 400);
                    }
                } else {
                    return response()->json(['message' => 'Order not found'], 404);
                }
            } else {
                return response()->json(['message' => 'Missing payment details'], 400);
            }
        } else {
            return response()->json(['message' => 'Invalid order code'], 404);
        }
    }


}
