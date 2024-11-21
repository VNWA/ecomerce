<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'product',
        'name',
        'phone',
        'email',
        'message'
    ];
    protected $casts = [
        'product' => 'array', // Chuyển các trường JSON thành mảng
    ];
}
