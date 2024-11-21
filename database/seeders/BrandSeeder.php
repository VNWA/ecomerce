<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run()
    {
        Brand::factory()->count(10)->create(); // Tạo 10 thương hiệu giả lập
    }
}
