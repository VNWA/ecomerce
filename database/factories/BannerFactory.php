<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Banner;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Banner>
 */
class BannerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Banner::class;

    public function definition(): array
    {
        return [
            'ord' => $this->faker->unique()->numberBetween(1, 10), // Đảm bảo giá trị ord là duy nhất
            'image' => $this->faker->imageUrl(800, 600, 'business'),
            'image_mobile' => $this->faker->imageUrl(400, 300, 'business'),
            'is_show' => $this->faker->boolean(80), // 80% khả năng là true
            'name' => $this->faker->word,
            'link' => $this->faker->url,
            'desc' => $this->faker->sentence,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
