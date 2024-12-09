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
            $table->tinyInteger('is_seller')->default( 0);
            $table->json('images');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null');
            $table->foreignId('color_id')->nullable()->constrained('colors')->onDelete('set null');
            $table->string('sku', 191)->unique();
            $table->string('size')->nullable();
            $table->string('included')->nullable();
            $table->integer('stock')->default(0);
            $table->tinyInteger('availability')->default(1);
            $table->string('origin');
            $table->string('ean')->nullable();
            $table->text('name');
            $table->text('slug')->unique();
            $table->integer('price');
            $table->tinyInteger('is_discount')->default( 0);
            $table->enum('discount_type', ['percentage', 'amount'])->nullable(); // Sử dụng enum cho discount_type
            $table->integer('discount_price')->nullable();
            $table->longText('description')->nullable();
            $table->longText('ingredients')->nullable();
            $table->longText('how_to_use')->nullable();
            $table->text('meta_image')->nullable();
            $table->text('meta_title')->nullable();
            $table->text('meta_desc')->nullable();
            $table->fullText(['name', 'slug', 'sku']);
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
