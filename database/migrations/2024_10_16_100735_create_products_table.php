<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('is_show')->default(1);
            $table->tinyInteger('is_seller')->default(0);
            $table->json('images');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null');
            $table->foreignId('color_id')->nullable()->constrained('colors')->onDelete('set null');
            $table->string('sku', 191)->unique(); // Chuyển sang VARCHAR với độ dài 191
            $table->string('size')->nullable();
            $table->string('included')->nullable();
            $table->integer('stock')->default(0);
            $table->tinyInteger('availability')->default(1);
            $table->string('origin');
            $table->string('ean')->nullable();
            $table->string('name'); // Chuyển từ text sang string
            $table->string('slug', 191)->unique(); // Chuyển từ TEXT sang VARCHAR với độ dài 191
            $table->integer('price');
            $table->tinyInteger('is_discount')->default(0);
            $table->enum('discount_type', ['percentage', 'amount'])->nullable();
            $table->integer('discount_price')->nullable();
            $table->longText('description')->nullable();
            $table->longText('ingredients')->nullable();
            $table->longText('how_to_use')->nullable();
            $table->string('meta_image')->nullable(); // Chuyển từ text sang string
            $table->string('meta_title')->nullable(); // Chuyển từ text sang string
            $table->string('meta_desc')->nullable(); // Chuyển từ text sang string
            $table->fullText(['name', 'slug', 'sku']); // Full-text search vẫn giữ nguyên
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
