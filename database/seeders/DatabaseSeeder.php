<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BlogCategorySeeder::class,
            BlogTagSeeder::class,
            BlogPostSeeder::class,
            AppearanceOneSeeder::class,
            ProductCategorySeeder::class,
            BrandSeeder::class,
            ColorSeeder::class,
            ProductSeeder::class,
            BannersTableSeeder::class,
            SettingSeeder::class,
        ]);
        User::truncate();
        User::create([
            'name' => 'DEV',
            'email' => 'nhatnguyen.dev.fullstack@gmail.com',
            'password' => Hash::make('dev@123'),
        ]);
        User::create([
            'name' => 'Admin',
            'email' => 'admin@vinawebapp.com',
            'password' => Hash::make('admin@123'),
        ]);
    }
}
