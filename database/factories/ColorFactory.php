<?php

namespace Database\Factories;

use App\Models\Color;
use Illuminate\Database\Eloquent\Factories\Factory;

class ColorFactory extends Factory
{
    protected $model = Color::class;

    public function definition()
    {
        return [
            'image' => $this->faker->imageUrl(), // Sử dụng Faker để tạo URL hình ảnh
            'name' => $this->faker->unique()->colorName(), // Tạo tên màu sắc duy nhất
            'desc' => $this->faker->sentence(), // Tạo mô tả ngắn
            'slug' => $this->faker->unique()->slug(), // Tạo slug duy nhất
        ];
    }
}
