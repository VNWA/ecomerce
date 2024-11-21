<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryCountry extends Model
{
    use HasFactory;
    protected $fillable = ['name'];



    public function deliveryPrices()
    {
        return $this->hasMany(DeliveryPrice::class);
    }

    // Lấy danh sách deliveries qua bảng trung gian DeliveryPrice
    public function deliveries()
    {
        return $this->hasManyThrough(
            Delivery::class,
            DeliveryPrice::class,
            'delivery_country_id', // Khóa ngoại trên delivery_prices
            'id',                  // Khóa chính trên deliveries
            'id',                  // Khóa chính trên delivery_countries
            'delivery_id'          // Khóa ngoại trên delivery_prices
        );
    }
}
