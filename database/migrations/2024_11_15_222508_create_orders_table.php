<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->enum('status', ['new', 'processing', 'shipped', 'completed', 'cancelled', 'returned'])->default('new');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email', 191);
            $table->string('phone', 20);
            $table->string('post_code', 20);
            $table->string('city');
            $table->string('address', 255);
            $table->string('address_number', 50);
            $table->string('note')->nullable();
            $table->tinyInteger('is_coupon')->default(0);
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->onDelete('set null');
            $table->json('coupon')->nullable();
            $table->json('delivery');
            $table->string('tracking_link')->nullable();
            $table->string('payment_method');
            $table->decimal('total', 10, 2);
            $table->enum('payment_status', ['pending', 'completed', 'cancelled', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
