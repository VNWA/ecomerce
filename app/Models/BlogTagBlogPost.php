<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogTagBlogPost extends Model
{
    use HasFactory;
    protected $table = 'blog_tag_blog_post';
    protected $fillable = [
        'blog_post_id',
        'blog_tag_id'
    ];

}
