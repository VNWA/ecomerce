<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Url;

class BlogCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'parent_id',
        'ord',
        'is_show',
        'is_highlight',
        'image',
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
    public function blogPosts()
    {
        return $this->belongsToMany(BlogPost::class, 'blog_category_blog_post', 'blog_category_id', 'blog_post_id');
    }
    protected static function boot()
    {
        parent::boot();

        // Tạo ord khi tạo mới
        static::creating(function ($blogCategory) {
            $parentId = $blogCategory->parent_id;
            $maxOrd = self::where('parent_id', $parentId)->max('ord');
            $blogCategory->ord = $maxOrd + 1;
        });

        static::created(function ($blogCategory) {
            $blogCategory->url()->create([
                'slug' => $blogCategory->slug,
                'model_type' => get_class($blogCategory),
                'model_id' => $blogCategory->id
            ]);
        });

        static::updated(function ($blogCategory) {
            $blogCategory->url()->update([
                'slug' => $blogCategory->slug
            ]);
        });

        static::deleted(function ($blogCategory) {
            $blogCategory->url()->delete();
        });
    }

    public function url()
    {
        return $this->morphOne(Url::class, 'model');
    }

}
