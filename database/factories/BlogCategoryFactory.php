<?php

namespace Database\Factories;

use App\Models\BlogCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogCategory>
 */
class BlogCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = BlogCategory::class;

     public function definition(): array
    {
        return [
            'parent_id' => null,
            'ord' => 0,
            'is_show' => 1,
            'is_highlight' => $this->faker->boolean,
            'image' => $this->faker->imageUrl(),
            'name' => $this->faker->word,
            'slug' => $this->faker->slug,
            'desc' => $this->faker->paragraph,
            'meta_image' => $this->faker->imageUrl(),
            'meta_title' => $this->faker->sentence,
            'meta_desc' => $this->faker->paragraph,
            // 'icon' => json_encode(['icon_name' => $this->faker->word, 'icon_type' => $this->faker->word])
        ];
    }
}
