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
    Schema::create('borrows', function (Blueprint $table) {
        $table->id(); // ID duy nhất [cite: 39]
        
        // Khóa ngoại đến bảng users [cite: 42, 45, 56]
        $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
        
        // Khóa ngoại đến bảng books [cite: 44, 46, 57]
        $table->foreignId('book_id')->constrained('books')->onDelete('restrict');
        
        $table->date('borrow_date'); // Ngày mượn [cite: 47]
        $table->date('due_date'); // Ngày hẹn trả [cite: 48]
        $table->date('return_date')->nullable(); // Ngày thực tế trả sách [cite: 49]
        // Trạng thái mượn mặc định [cite: 51]
        $table->enum('status', ['pending', 'borrowed', 'returned', 'overdue'])->default('borrowed');
        $table->text('notes')->nullable(); // Ghi chú [cite: 51]
        $table->timestamps(); // [cite: 52, 53]

        // Tạo indexes cho các trường thường query [cite: 75]
        $table->index('user_id');
        $table->index('book_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrows');
    }
};
