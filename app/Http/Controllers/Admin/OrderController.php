<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\DeliveryCountry;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Validator;

class OrderController extends Controller
{
    public function loadOrderData($code)
    {
        $order = Order::where('code', $code)->first();
        return response()->json([
            'logs' => $order->logs,
            'items' => $order->items,
        ], 200);

    }
    public function loadDataTable(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sortBy', 'created_at');
        $sortType = $request->get('sortType', 'desc');
        $code = $request->get('code');
        $phone = $request->get('phone');
        $email = $request->get('email');
        $city = $request->get('city');
        $payment_method = $request->get('payment_method');
        $payment_status = $request->get('payment_status');
        $status = $request->get('status');
        $query = Order::query();
        if ($code) {
            $query->where('code', 'like', "%$code%");
        }
        if ($phone) {
            $query->where('phone', 'like', "%$phone%");
        }
        if ($email) {
            $query->where('email', 'like', "%$email%");
        }
        if ($city) {
            $query->where('city', 'like', "%$city%");
        }
        if ($payment_method) {
            $query->where('payment_method', 'like', "%$payment_method%");
        }
        if ($payment_status) {
            $query->where('payment_status', 'like', "%$payment_status%");
        }
        if ($status) {
            $query->where('status', 'like', "%$status%");
        }



        // Sắp xếp và phân trang
        $total = $query->count();
        $orders = $query->skip(($page - 1) * $perPage)->take($perPage)->orderBy($sortBy, $sortType)->get();

        return response()->json([
            'data' => $orders,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => ceil($total / $perPage),
        ], 200);


    }
    public function findProducts($s = '')
    {

        $products = Product::query();

        // Nếu có tham số tìm kiếm, áp dụng điều kiện where like
        if ($s) {
            $products->where('name', 'like', '%' . $s . '%');
        }

        // Giới hạn kết quả trả về
        $products = $products->take(10)->get(); // Lấy tối đa 10 sản phẩm
        $products = $products->map(function ($product) {
            $images = $product->images;
            $product->image = $images[0];
            return [
                'image' => $images[0],
                'name' => $product->name,
                'slug' => $product->slug,
                'sku' => $product->sku,
                'price' => $product->price,
                'price_new' => $product->price_new,
            ];
        });
        // Trả về dữ liệu
        return response()->json([
            'message' => 'success ' . $s,
            'products' => $products
        ], 200);
    }

    public function loadOrderFormFileds()
    {
        $deliveryCountries = DeliveryCountry::with(['deliveryPrices.delivery'])->get();
        $deliveries = $deliveryCountries->map(function ($country) {
            return [
                'value' => $country->id,
                'title' => $country->name,
                'deliveries' => $country->deliveryPrices->map(function ($deliveryPrice) {
                    return [
                        'id' => $deliveryPrice->delivery->id,
                        'name' => $deliveryPrice->delivery->name,
                        'image' => $deliveryPrice->delivery->image,
                        'price' => $deliveryPrice->price,
                    ];
                }),
            ];

        });
        $products = Product::where('is_show', 1)->latest()->take(10)->get();
        $products = $products->map(function ($product) {
            $images = $product->images;
            $product->image = $images[0];
            return [
                'image' => $images[0],
                'name' => $product->name,
                'slug' => $product->slug,
                'sku' => $product->sku,
                'price' => $product->price,
                'price_new' => $product->price_new,
            ];
        });
        $payments = [
            [
                'name' => 'Paypal',
                'desc' => 'Thanh toán bằng paypal',
                'value' => 'paypal',
                'image' => url('/images/payment/paypal.webp')
            ],
            [
                'name' => 'Stripe',
                'desc' => 'Thanh toán bằng stripe',
                'value' => 'stripe',
                'image' => url('/images/payment/stripe.webp')
            ]
        ];
        $coupons = Coupon::where('qnt', '>', 0) // Số lượng phải lớn hơn 0
            ->where(function ($query) {
                $query->where('is_duration', 0)
                    ->orWhere(function ($query) {
                        $query->where('is_duration', 1)
                            ->where('start_time', '<=', Carbon::now())
                            ->where('end_time', '>=', Carbon::now());
                    });
            })
            ->get();
        return response()->json(['deliveries' => $deliveries, 'coupons' => $coupons, 'products' => $products, 'payments' => $payments], 200);
    }
    function index()
    {
        return Inertia::render('Admin/Ecommerce/Order/Show');
    }
    public function viewCreate()
    {
        return Inertia::render('Admin/Ecommerce/Order/Create');

    }
    public function store(Request $request)
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
                $product = Product::where('sku', $item['sku'])->firstOrFail();
                if ($product) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'image' => $product->images[0],
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'sku' => $product->sku,
                        'price' => $product->price_new,
                        'quantity' => $item['quantity'],

                    ]);

                } else {
                    DB::rollBack();
                    return response()->json([
                        'message' => $item['name'] . ' not found.',
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

    public function loadOrderDetail($id)
    {
        $order = Order::find($id);
        return response()->json([
            'message' => 'success ' . $id,
            'order' => $order
        ], 200);
    }
    public function viewCopy($id)
    {
        $order = Order::with(['items'])->find($id);

        return Inertia::render('Admin/Ecommerce/Order/Copy', ['order' => $order]);

    }
    public function viewEdit($id)
    {
        $order = Order::with(['items'])->find($id);

        return Inertia::render('Admin/Ecommerce/Order/Edit', ['order' => $order]);

    }
    public function update($id, $type)
    {
        try {
            $order = Order::find($id);
            switch ($type) {
                case 'payment.completed':
                    $order->payment_status = 'completed';
                    $order->status = 'processing';
                    $order->save();
                    break;
                case 'status.cancelled':
                    $order->status = 'cancelled';
                    $order->save();
                    break;
                case 'status.shipped':
                    $order->status = 'shipped';
                    $order->save();
                    break;
                case 'status.completed':
                    $order->status = 'completed';
                    $order->save();
                    break;

                default:
                    break;
            }
            return response()->json(['message' => 'Success'], 200);

        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
