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
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('banner_image')->nullable();
            $table->tinyInteger('is_show')->default(1);
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('desc')->nullable();
            $table->longText('content')->nullable();
            $table->integer('author_id')->nullable();
            $table->text('meta_image')->nullable();
            $table->text('meta_title')->nullable();
            $table->text('meta_desc')->nullable();
            $table->timestamps();
            // Indexes
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
