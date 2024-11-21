<?php

namespace App\Models;

use App\Mail\OrderCreatedMail;
use App\Mail\OrderStatusUpdatedMail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Mail;
use Tuupola\Base62;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'status',
        'first_name',
        'last_name',
        'email',
        'phone',
        'post_code',
        'city',
        'address',
        'address_number',
        'note',
        'is_coupon',
        'coupon_id',
        'coupon',
        'delivery',
        'payment_method',
        'total',
        'payment_status',
        'tracking_link',
    ];

    protected $casts = [
        'coupon' => 'array',
        'delivery' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['update_time'];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $base62 = new Base62();
            do {
                // Tạo số ngẫu nhiên và mã hóa Base62
                $randomNumber = random_int(100000, 999999);
                $code = 'VNWA-' . $base62->encode($randomNumber);
            } while (self::where('code', $code)->exists()); // Kiểm tra nếu mã bị trùng

            // Gán mã duy nhất cho đơn hàng
            $order->code = $code;
        });
        static::created(function ($order) {
            // Gửi email sau khi đơn hàng được tạo
            Mail::to($order->email)->queue(new OrderCreatedMail($order));
            OrderLog::create([
                'order_id' => $order->id,
                'field' => 'create',
                'message' => 'Order #' . $order->code . ' Created Successfully',
            ]);

        });
        static::updated(function ($order) {
            // Nếu trạng thái thanh toán thay đổi, gửi email thông báo
            if ($order->isDirty('payment_status')) {
                $message = '';
                switch ($order->payment_status) {
                    case 'pending':
                        $message = 'Đơn hàng ' . $order->code . ' chờ thanh toán';
                        break;
                    case 'completed':
                        $message = 'Đơn hàng ' . $order->code . ' thanh toán thành công';
                        break;
                    case 'cancelled':
                        $message = 'Đơn hàng ' . $order->code . ' thanh toán thất bại';
                        break;
                    case 'failed':
                        $message = 'Đơn hàng ' . $order->code . ' thanh toán thất bại';
                        break;
                    default:
                        $message = 'Đơn hàng ' . $order->code . ' đã được cập nhật trạng thái thanh toán';
                }

                // Log thông tin thay đổi trạng thái thanh toán
                OrderLog::create([
                    'order_id' => $order->id,
                    'field' => 'payment_status',
                    'message' => $message,
                ]);

                // Gửi email thông báo
                Mail::to($order->email)->queue(new OrderStatusUpdatedMail($order, $message));
            }

            // Nếu trạng thái đơn hàng thay đổi, gửi email thông báo
            if ($order->isDirty('status')) {
                $message = '';
                switch ($order->status) {
                    case 'processing':
                        $message = 'Đơn hàng ' . $order->code . ' đang được chuẩn bị';
                        break;
                    case 'shipped':
                        $message = 'Đơn hàng ' . $order->code . ' đang giao hàng';
                        break;
                    case 'completed':
                        $message = 'Đơn hàng ' . $order->code . ' đã giao hàng thành công';
                        break;
                    case 'cancelled':
                        $message = 'Đơn hàng ' . $order->code . ' đã hủy';
                        break;
                    case 'returned':
                        $message = 'Đơn hàng ' . $order->code . ' đã chuyển sang trạng thái hoàn trả';
                        break;
                    default:
                        $message = 'Đơn hàng ' . $order->code . ' đã được cập nhật trạng thái';
                }

                // Log thông tin thay đổi trạng thái đơn hàng
                OrderLog::create([
                    'order_id' => $order->id,
                    'field' => 'status',
                    'message' => $message,
                ]);

                // Gửi email thông báo
                Mail::to($order->email)->queue(new OrderStatusUpdatedMail($order, $message));
            }
        });

    }

    public function getUpdateTimeAttribute()
    {
        return Carbon::parse($this->updated_at)->format('H:i, d/m/Y');
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function logs()
    {
        return $this->hasMany(OrderLog::class)->select(['field', 'message']);
    }
    /**
     * Get the coupon associated with the order.
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
