<?php

namespace Database\Factories;

use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogPost>
 */
class BlogPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = BlogPost::class;

    public function definition(): array
    {
        return [
            'banner_image' => $this->faker->imageUrl(),
            'image' => $this->faker->imageUrl(),
            'is_show' => 1,
            'name' => $this->faker->word,
            'slug' => $this->faker->slug,
            'desc' => $this->faker->paragraph,
            'content' => $this->faker->paragraphs(3, true),
            'author_id' => $this->faker->randomDigitNotNull,
            'meta_image' => $this->faker->imageUrl(),
            'meta_title' => $this->faker->sentence,
            'meta_desc' => $this->faker->paragraph,
        ];
    }
}
