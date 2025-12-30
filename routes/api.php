<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BorrowController;

// Các routes không cần login (Public)
Route::post('/auth/register', [AuthController::class, 'register']); // [cite: 85]
Route::post('/auth/login', [AuthController::class, 'login']); // [cite: 117]

// Các routes yêu cầu Authentication (Sử dụng middleware sanctum) [cite: 147, 148]
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']); // [cite: 197]
});




Route::middleware(['auth:sanctum'])->group(function () {
    
    // Các route dành cho mọi người dùng đã đăng nhập (authenticated users) [cite: 154]
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Các route CHỈ dành cho Admin (sử dụng middleware 'admin' vừa tạo)
    Route::middleware(['admin'])->group(function () {
        Route::post('/books', [BookController::class, 'store']);     // Thêm sách [cite: 218]
        Route::put('/books/{id}', [BookController::class, 'update']); // Cập nhật sách [cite: 239]
        Route::delete('/books/{id}', [BookController::class, 'destroy']); // Xóa sách [cite: 254]
        
        // Quản lý Users (Phần 3)
        Route::get('/users', [UserController::class, 'index']); // Lấy DS user [cite: 161]
    });
});


// 1. Public Routes (Ai cũng xem được)
Route::get('/books', [BookController::class, 'index']); // Danh sách + Search
Route::get('/books/{id}', [BookController::class, 'show']); // Chi tiết

// 2. Protected Routes (Yêu cầu đăng nhập và quyền Admin)
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/books', [BookController::class, 'store']); // Thêm sách
    Route::put('/books/{id}', [BookController::class, 'update']); // Sửa sách
    Route::delete('/books/{id}', [BookController::class, 'destroy']); // Xóa sách
});



Route::middleware('auth:sanctum')->group(function () {
    // API Mượn sách
    Route::post('/borrows', [BorrowController::class, 'borrow']); 
    
    // API Trả sách
    Route::put('/borrows/{id}/return', [BorrowController::class, 'returnBook']);
});