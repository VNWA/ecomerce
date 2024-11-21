<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'parent_id',
        'ord',
        'is_show',
        'is_highlight',
        'is_header',
        'is_header_sub_colors',
        'header_bg',
        'image',
        'banner_image',
        'name',
        'content',
        'slug',
        'desc',
        'meta_image',
        'meta_title',
        'meta_desc',
        'icon'
    ];
    protected $casts = [
        'icon' => 'array', // Chuyển các trường JSON thành mảng
    ];
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_category_assignments', 'product_category_id', 'product_id');
    }
    protected static function boot()
    {
        parent::boot();

        // Tạo ord khi tạo mới
        static::creating(function ($productCategory) {
            $parentId = $productCategory->parent_id;
            $maxOrd = self::where('parent_id', $parentId)->max('ord');
            $productCategory->ord = $maxOrd + 1;
        });

        static::created(function ($productCategory) {
            $productCategory->url()->create([
                'slug' => $productCategory->slug,
                'model_type' => get_class($productCategory),
                'model_id' => $productCategory->id
            ]);
        });

        static::updated(function ($productCategory) {
            $productCategory->url()->update([
                'slug' => $productCategory->slug
            ]);
        });

        static::deleted(function ($productCategory) {
            $productCategory->url()->delete();
        });
    }

    public function url()
    {
        return $this->morphOne(Url::class, 'model');
    }
}
