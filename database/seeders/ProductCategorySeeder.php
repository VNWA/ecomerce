<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductCategory::factory()
        ->count(10)
        ->create()
        ->each(function ($productCategory) {
            ProductCategory::factory()
                ->count(5)
                ->create(['parent_id' => $productCategory->id])
                ->each(function ($subCategory) use ($productCategory) {
                    // Cập nhật giá trị 'ord' cho danh mục con
                    $maxOrd = ProductCategory::where('parent_id', $subCategory->parent_id)->max('ord');
                    $subCategory->ord = $maxOrd + 1;
                    $subCategory->save();

                });

            // Cập nhật giá trị 'ord' cho danh mục cha
            $maxOrd = ProductCategory::whereNull('parent_id')->max('ord');
            $productCategory->ord = $maxOrd + 1;
            $productCategory->save();
        });
    }
}
