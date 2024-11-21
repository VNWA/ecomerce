<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductCategory>
 */
class ProductCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = ProductCategory::class;

    public function definition(): array
    {
        return [
            'parent_id' => null,
            'ord' => 0,
            'is_show' => 1,
            'is_highlight' => $this->faker->boolean,
            'image' => $this->faker->imageUrl(800, 800), // Hình ảnh vuông
            'banner_image' => $this->faker->imageUrl(1200, 400), // Hình chữ nhật ngang
            'name' => $this->faker->sentence(3), // Tên dài hơn
            'slug' => $this->faker->slug,
            'desc' => $this->faker->paragraph,
            'meta_image' => $this->faker->imageUrl(),
            'meta_title' => $this->faker->sentence,
            'meta_desc' => $this->faker->paragraph,
            // 'icon' => json_encode(['icon_name' => $this->faker->word, 'icon_type' => $this->faker->word])
        ];
    }
}
