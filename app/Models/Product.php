<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'is_show',
        'is_seller',
        'images',
        'brand_id',
        'color_id',
        'sku',
        'size',
        'included',
        'stock',
        'availability',
        'origin',
        'ean',
        'name',
        'slug',
        'price',
        'is_discount',
        'discount_type',
        'discount_price',
        'description',
        'ingredients',
        'how_to_use',
        'meta_image',
        'meta_title',
        'meta_desc'
    ];


    protected $casts = [
        'images' => 'array', // Chuyển các trường JSON thành mảng
        'catalogs' => 'array',
    ];


    protected $appends = ['price_new', 'is_new']; // Thêm price_new vào JSON

    /**
     * Accessor cho `price_new`
     */
    public function getIsNewAttribute()
    {
        // Chuyển đổi 'created_at' thành Carbon, sử dụng định dạng ngày tháng của bạn
        try {
            $createdAt = Carbon::createFromFormat('d/m/Y', $this->created_at);

            // Kiểm tra xem sản phẩm có được tạo trong tháng hiện tại không
            return $createdAt->isCurrentMonth();
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function getPriceNewAttribute()
    {
        // Nếu không có giảm giá, trả về giá gốc
        if ($this->is_discount == 0) {
            return $this->price;
        }

        // Tính toán giá mới dựa trên loại giảm giá
        if ($this->discount_type === 'amount') {
            // Giảm theo số tiền
            $newPrice = $this->price - $this->discount_price;
        } elseif ($this->discount_type === 'percentage') {
            // Giảm theo phần trăm
            $discount = $this->price * ($this->discount_price / 100);
            $newPrice = $this->price - $discount;
        } else {
            // Trường hợp không khớp loại giảm giá, trả về giá gốc
            $newPrice = $this->price;
        }

        // Kiểm tra nếu giá mới nhỏ hơn 0, đặt giá trị mặc định là 0
        return max($newPrice, 0);
    }

    protected static function boot()
    {
        parent::boot();

        // Sự kiện khi tạo mới
        static::created(function ($product) {
            $product->url()->create([
                'slug' => $product->slug,
                'model_type' => get_class($product),
                'model_id' => $product->id
            ]);
        });

        // Sự kiện khi cập nhật
        static::updated(function ($product) {
            $product->url()->update([
                'slug' => $product->slug
            ]);
        });

        // Sự kiện khi xóa
        static::deleted(function ($product) {
            $product->url()->delete();
        });
    }

    public function categories()
    {
        return $this->belongsToMany(ProductCategory::class, 'product_category_assignments', 'product_id', 'product_category_id')
            ->select('product_categories.id', 'product_categories.name', 'product_categories.slug');
    }
    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function url()
    {
        return $this->morphOne(Url::class, 'model');
    }
    public function getCreatedAtAttribute($value)
    {
        return $this->formatDate($value);
    }

    // Accessor cho `updated_at`
    public function getUpdatedAtAttribute($value)
    {
        return $this->formatDate($value);
    }

    // Hàm định dạng ngày giờ tùy thuộc vào điều kiện
    private function formatDate($value)
    {
        $date = Carbon::parse($value);
        if ($date->isToday()) {
            return $date->diffForHumans();
        } else {
            return $date->format('d/m/Y');
        }
    }

}
