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
                        $message = 'Order ' . $order->code . ' is pending payment.';
                        break;
                    case 'completed':
                        $message = 'Order ' . $order->code . ' has been successfully paid.';
                        break;
                    case 'cancelled':
                        $message = 'Order ' . $order->code . ' payment was cancelled.';
                        break;
                    case 'failed':
                        $message = 'Order ' . $order->code . ' payment failed.';
                        break;
                    default:
                        $message = 'Order ' . $order->code . ' payment status has been updated.';
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
                        $message = 'Order ' . $order->code . ' is being processed.';
                        break;
                    case 'shipped':
                        $message = 'Order ' . $order->code . ' is being shipped.';
                        break;
                    case 'completed':
                        $message = 'Order ' . $order->code . ' has been successfully delivered.';
                        break;
                    case 'cancelled':
                        $message = 'Order ' . $order->code . ' has been cancelled.';
                        break;
                    case 'returned':
                        $message = 'Order ' . $order->code . ' has been marked as returned.';
                        break;
                    default:
                        $message = 'Order ' . $order->code . ' status has been updated.';
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
