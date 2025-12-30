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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // ID duy nhất [cite: 15]
            $table->string('email')->unique(); // Email đăng nhập [cite: 17, 75]
            $table->string('password'); // Mật khẩu đã hash [cite: 18]
            $table->string('full_name'); // Họ và tên đầy đủ [cite: 19]
            $table->string('phone_number')->nullable(); // Số điện thoại [cite: 20]
            $table->string('address')->nullable(); // Địa chỉ [cite: 21]
            // Vai trò mặc định là student [cite: 22]
            $table->enum('role', ['student', 'teacher', 'admin'])->default('student'); 
            $table->timestamps(); // Thời gian tạo và cập nhật [cite: 23, 24]
            
            $table->index('email'); // Tạo index cho email [cite: 75]
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
