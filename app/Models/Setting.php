<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['type', 'value','json_value'];


    protected $casts = [
        'json_value' => 'array',
    ];
}
