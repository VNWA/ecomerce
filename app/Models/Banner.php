<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;
    protected $fillable = ['ord', 'is_show', 'image', 'image_mobile', 'name', 'desc', 'link'];
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Lấy giá trị max hiện tại của cột ord và tăng lên 1
            $model->ord = self::max('ord') + 1;
        });
    }

}
