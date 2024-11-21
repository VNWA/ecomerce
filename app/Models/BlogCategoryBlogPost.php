<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategoryBlogPost extends Model
{
    use HasFactory;
    protected $table = 'blog_category_blog_post';
    protected $fillable = [
        'blog_post_id',
        'blog_category_id'
    ];
}
