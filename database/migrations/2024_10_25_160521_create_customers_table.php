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

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->timestamp('email_verified_at')->nullable(); // Trường để lưu thời gian xác thực email
            $table->string('password'); // Mật khẩu được mã hóa
            $table->string('company')->nullable(); // Công ty của khách hàng
            $table->string('post_code'); // Mã bưu điện
            $table->string('city'); // Thành phố
            $table->string('address'); // Địa chỉ
            $table->string('address_number')->nullable(); // Số nhà
            $table->string('otp')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->rememberToken(); // Trường để lưu token cho tính năng "Nhớ tôi"
            $table->timestamps(); // Timestamps cho created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
