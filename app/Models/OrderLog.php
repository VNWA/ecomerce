<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'field',
        'message',
    ];
    protected $appends = ['update_time'];
    public function getUpdateTimeAttribute()
    {
        return Carbon::parse($this->updated_at)->format('H:i, d/m/Y');
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
