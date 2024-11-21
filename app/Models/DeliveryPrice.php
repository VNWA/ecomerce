<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryPrice extends Model
{
    use HasFactory;
    protected $fillable = ['delivery_id', 'delivery_country_id', 'price'];


    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function delivery_country()
    {
        return $this->belongsTo(DeliveryCountry::class);
    }
}
