<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogTag extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'desc',
    ];
    protected static function boot()
    {
        parent::boot();

        // Sự kiện khi tạo mới
        static::created(function ($blogTag) {
            $blogTag->url()->create([
                'slug' => $blogTag->slug,
                'model_type' => get_class($blogTag),
                'model_id' => $blogTag->id
            ]);
        });

        // Sự kiện khi cập nhật
        static::updated(function ($blogTag) {
            $blogTag->url()->update([
                'slug' => $blogTag->slug
            ]);
        });

        // Sự kiện khi xóa
        static::deleted(function ($blogTag) {
            $blogTag->url()->delete();
        });
    }

    public function blogs()
    {
        return $this->belongsToMany(BlogPost::class, 'blog_tag_blog_post', 'blog_tag_id', 'blog_post_id');
    }

    public function url()
    {
        return $this->morphOne(Url::class, 'model');
    }
}
