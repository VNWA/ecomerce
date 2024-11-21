<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition()
    {
        return [
            'image' => $this->faker->imageUrl(640, 480, 'business', true), // URL hình ảnh giả lập
            'name' => $this->faker->company(), // Tên thương hiệu giả lập
            'desc' => $this->faker->sentence(), // Mô tả giả lập
            'slug' => $this->faker->slug, // Liên kết website giả lập
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
