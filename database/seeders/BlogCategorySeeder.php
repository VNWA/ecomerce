<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlogCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BlogCategory::factory()
        ->count(10)
        ->create()
        ->each(function ($blogCategory) {
            BlogCategory::factory()
                ->count(5)
                ->create(['parent_id' => $blogCategory->id])
                ->each(function ($subCategory) use ($blogCategory) {
                    // Cập nhật giá trị 'ord' cho danh mục con
                    $maxOrd = BlogCategory::where('parent_id', $subCategory->parent_id)->max('ord');
                    $subCategory->ord = $maxOrd + 1;
                    $subCategory->save();

                });

            // Cập nhật giá trị 'ord' cho danh mục cha
            $maxOrd = BlogCategory::whereNull('parent_id')->max('ord');
            $blogCategory->ord = $maxOrd + 1;
            $blogCategory->save();
        });
    }
}
