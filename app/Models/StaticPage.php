<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaticPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'is_show',
        'is_header',
        'is_header_main',
        'name',
        'slug',
        'desc',
        'content',
        'meta_image',
        'meta_title',
        'meta_desc'
    ];


    public static function setHeader($id)
    {
        // Đặt tất cả các hàng khác về 0
        self::where('id', '!=', $id)->update(['is_header_main' => 0]);

        // Đặt hàng cụ thể thành 1
        $page = self::find($id);
        if ($page) {
            $page->is_header_main = 1;
            $page->is_header = 1;
            $page->save();
        }
    }
}
