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
    Schema::create('books', function (Blueprint $table) {
        $table->id(); // ID duy nhất [cite: 26]
        $table->string('title'); // Tiêu đề sách [cite: 27]
        $table->string('author'); // Tác giả [cite: 29]
        $table->string('isbn')->unique(); // Mã ISBN duy nhất [cite: 30, 75]
        // Thể loại sách [cite: 31]
        $table->enum('category', ['Novel', 'Science', 'History', 'Technology']); 
        $table->text('description')->nullable(); // Mô tả sách [cite: 31]
        $table->integer('total_copies')->default(1); // Tổng bản copy [cite: 32]
        $table->integer('available_copies')->default(1); // Bản copy còn lại [cite: 33]
        $table->integer('published_year')->nullable(); // Năm xuất bản [cite: 34]
        $table->string('publisher')->nullable(); // Nhà xuất bản [cite: 35]
        $table->timestamps(); // [cite: 36, 37]

        $table->index('isbn'); // Index cho isbn [cite: 75]
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
