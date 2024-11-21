<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Color;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'is_show' => $this->faker->boolean(),
            'images' => [$this->faker->imageUrl(800, 800), $this->faker->imageUrl(800, 800)],
            'brand_id' => Brand::inRandomOrder()->first()->id, // Lấy ID ngẫu nhiên từ bảng brands
            'color_id' => Color::inRandomOrder()->first()->id, // Lấy ID ngẫu nhiên từ bảng colors
            'sku' => $this->faker->unique()->bothify('SKU-##??'),
            'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL', null]),
            'included' => $this->faker->sentence(),
            'stock' => $this->faker->numberBetween(0, 100),
            'availability' => $this->faker->boolean(),
            'origin' => $this->faker->country(),
            'ean' => $this->faker->ean13(),
            'name' => $this->faker->word() . ' ' . $this->faker->word(),
            'slug' => Str::slug($this->faker->unique()->word() . ' ' . $this->faker->word()),
            'price' => $this->faker->numberBetween(10000, 1000000),
            'is_discount' => $this->faker->boolean(),
            'discount_type' => $this->faker->randomElement(['percentage', 'amount', null]),
            'discount_price' => $this->faker->numberBetween(1000, 100000) ?: null,
            'description' => $this->faker->paragraph(),
            'ingredients' => $this->faker->paragraph(),
            'how_to_use' => $this->faker->paragraph(),
            'meta_image' => $this->faker->imageUrl(),
            'meta_title' => $this->faker->sentence(),
            'meta_desc' => $this->faker->sentence(),
        ];
    }
}
