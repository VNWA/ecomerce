<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use DB;
use Http;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;
use Validator;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
class OrderController extends Controller
{
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
                $product = $item['product'];
                if (Product::where('sku', $key)->exists()) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'image' => $product['image'],
                        'name' => $product['name'],
                        'slug' => $product['slug'],
                        'sku' => $product['sku'],
                        'price' => $product['price_new'],
                        'quantity' => $item['qnt'],

                    ]);

                } else {
                    DB::rollBack();
                    return response()->json([
                        'message' => $product['name'] . ' not found.',
                    ], 500);
                }
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
    public function stripeWebhook(Request $request)
    {
        // Lấy mã bí mật của webhook từ Stripe Dashboard
        $endpoint_secret = env('STRIPE_WEBHOOK_KEY');

        // Lấy raw body của webhook request
        $payload = $request->getContent();

        // Lấy chữ ký của Stripe để kiểm tra tính hợp lệ của webhook
        $sig_header = $request->header('Stripe-Signature');

        try {
            // Xác minh chữ ký của Stripe
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (SignatureVerificationException $e) {
            // Nếu chữ ký không hợp lệ, trả về lỗi 400
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Kiểm tra sự kiện và xử lý theo yêu cầu
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object; // Thông tin về session
                $orderCode = $session->metadata->order_code; // Mã đơn hàng

                // Kiểm tra trạng thái thanh toán
                if ($session->payment_status == 'paid') {
                    // Cập nhật trạng thái đơn hàng trong cơ sở dữ liệu là "đã thanh toán"
                    $order = Order::where('code', $orderCode)->first();
                    if ($order) {
                        $order->payment_status = 'completed'; // Cập nhật trạng thái thanh toán
                        $order->save(); // Lưu và kích hoạt sự kiện
                    }
                }
                break;

            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $orderCode = $paymentIntent->metadata->order_code;
                $order = Order::where('code', $orderCode)->first();
                if ($order) {
                    $order->payment_status = 'completed'; // Cập nhật trạng thái thanh toán
                    $order->save(); // Lưu và kích hoạt sự kiện
                }
                break;

            default:
                // Các sự kiện khác mà bạn không quan tâm
                break;
        }

        // Trả về HTTP 200 để Stripe biết rằng webhook đã được nhận và xử lý thành công
        return response()->json(['status' => 'success']);
    }

    private function stripeCreatePaymentLink($orderCode, $orderPrice)
    {
        // Khởi tạo StripeClient
        $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));

        // Tạo một Price mới (nếu chưa có trước đó)
        $price = $stripe->prices->create([
            'unit_amount' => $orderPrice * 100, // Stripe yêu cầu giá trị tính bằng cent
            'currency' => 'usd',
            'product_data' => [
                'name' => $orderCode, // Đặt tên sản phẩm là mã đơn hàng
            ],
        ]);

        // Tạo Payment Link
        $paymentLink = $stripe->paymentLinks->create([
            'line_items' => [
                [
                    'price' => $price->id, // Sử dụng Price ID được tạo ở trên
                    'quantity' => 1,
                ],
            ],
            'metadata' => [
                'order_code' => $orderCode, // Có thể thêm metadata để theo dõi thông tin đơn hàng
            ],
            'after_completion' => [
                'type' => 'redirect',
                'redirect' => ['url' => env('FRONTEND_URL', 'https://vinawebapp.com')],
            ],
        ]);

        return $paymentLink->url;
    }

    public function test($orderID = null)
    {
        return $this->stripeCreatePaymentLink('Jsonaaa', 100);
    }
    public function detail($code)
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
                    $paymentUrl = $this->stripeCreatePaymentLink($order->code, $order->total);
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
    public function paymenRedirect($status, $orderCode)
    {
        try {
            if ($status == 'success') {

                $order = Order::where('code', $orderCode)->where('payment_status', 'pending')->first();
                if ($order) {
                    $order->payment_status = 'completed';
                    $order->save();
                    return redirect(env('FRONTEND_URL', 'https://vinawebapp.com') . '/order/success');
                }

            }
            return redirect(env('FRONTEND_URL', 'https://vinawebapp.com') . '/order/cancel');
        } catch (\Throwable $th) {
            return redirect(env('FRONTEND_URL', 'https://vinawebapp.com') . '/order/cancel');

        }

    }


    private function paypalVerifyPayment($orderID)
    {
        $provider = new PayPalClient;

        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        // Capture Payment with paymentId and payerId
        $response = $provider->showOrderDetails($orderID);

        // Kiểm tra trạng thái trả về
        return $response;
        if (empty($response['error'])) {
            if ($response['status'] == 'COMPLETED') {
                return true;
            }
        }
        return false;
    }


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
                    if ($this->paypalVerifyPayment($orderID)) {
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
