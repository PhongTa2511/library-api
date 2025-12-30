<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Book;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo 5 Users mẫu (1 admin, 2 teachers, 2 students) [cite: 78]
        // Mật khẩu được hash bằng bcrypt theo yêu cầu bảo mật [cite: 97, 397]
        $users = [
            ['email' => 'admin@library.com', 'password' => Hash::make('password123'), 'full_name' => 'Nguyen Admin', 'role' => 'admin'],
            ['email' => 'teacher1@library.com', 'password' => Hash::make('password123'), 'full_name' => 'Tran Giao Vien A', 'role' => 'teacher'],
            ['email' => 'teacher2@library.com', 'password' => Hash::make('password123'), 'full_name' => 'Le Giao Vien B', 'role' => 'teacher'],
            ['email' => 'student1@library.com', 'password' => Hash::make('password123'), 'full_name' => 'Pham Sinh Vien C', 'role' => 'student'],
            ['email' => 'student2@library.com', 'password' => Hash::make('password123'), 'full_name' => 'Hoang Sinh Vien D', 'role' => 'student'],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        // 2. Tạo 10 Books mẫu phân bổ theo các category [cite: 79, 31]
        $books = [
            ['title' => 'Dế Mèn Phiêu Lưu Ký', 'author' => 'Tô Hoài', 'isbn' => '9780001', 'category' => 'Novel', 'total_copies' => 5, 'available_copies' => 5],
            ['title' => 'Sapiens: Lược sử loài người', 'author' => 'Yuval Noah Harari', 'isbn' => '9780002', 'category' => 'History', 'total_copies' => 3, 'available_copies' => 3],
            ['title' => 'Clean Code', 'author' => 'Robert C. Martin', 'isbn' => '9780003', 'category' => 'Technology', 'total_copies' => 10, 'available_copies' => 10],
            ['title' => 'Vũ trụ', 'author' => 'Carl Sagan', 'isbn' => '9780004', 'category' => 'Science', 'total_copies' => 4, 'available_copies' => 4],
            ['title' => 'Tắt đèn', 'author' => 'Ngô Tất Tố', 'isbn' => '9780005', 'category' => 'Novel', 'total_copies' => 2, 'available_copies' => 2],
            ['title' => 'Lược sử thời gian', 'author' => 'Stephen Hawking', 'isbn' => '9780006', 'category' => 'Science', 'total_copies' => 5, 'available_copies' => 5],
            ['title' => 'Đại Việt Sử Ký Toàn Thư', 'author' => 'Ngô Sĩ Liên', 'isbn' => '9780007', 'category' => 'History', 'total_copies' => 2, 'available_copies' => 2],
            ['title' => 'Laravel Guide', 'author' => 'Taylor Otwell', 'isbn' => '9780008', 'category' => 'Technology', 'total_copies' => 7, 'available_copies' => 7],
            ['title' => 'Flutter Development', 'author' => 'Google', 'isbn' => '9780009', 'category' => 'Technology', 'total_copies' => 6, 'available_copies' => 6],
            ['title' => 'Số Đỏ', 'author' => 'Vũ Trọng Phụng', 'isbn' => '9780010', 'category' => 'Novel', 'total_copies' => 4, 'available_copies' => 4],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }

        // 3. Tạo 5 Borrows mẫu (bao gồm borrowed và returned) [cite: 80, 51]
        DB::table('borrows')->insert([
            [
                'user_id' => 4, // Student 1
                'book_id' => 3, // Clean Code
                'borrow_date' => Carbon::now()->subDays(10),
                'due_date' => Carbon::now()->subDays(3),
                'return_date' => Carbon::now()->subDays(4),
                'status' => 'returned',
                'created_at' => Carbon::now(),
            ],
            [
                'user_id' => 4, // Student 1
                'book_id' => 1, // Dế Mèn
                'borrow_date' => Carbon::now()->subDays(2),
                'due_date' => Carbon::now()->addDays(5),
                'return_date' => null,
                'status' => 'borrowed',
                'created_at' => Carbon::now(),
            ],
            [
                'user_id' => 5, // Student 2
                'book_id' => 9, // Flutter
                'borrow_date' => Carbon::now()->subDays(5),
                'due_date' => Carbon::now()->addDays(2),
                'return_date' => null,
                'status' => 'borrowed',
                'created_at' => Carbon::now(),
            ],
            [
                'user_id' => 2, // Teacher 1
                'book_id' => 4, // Vũ trụ
                'borrow_date' => Carbon::now()->subDays(15),
                'due_date' => Carbon::now()->subDays(8),
                'return_date' => Carbon::now()->subDays(8),
                'status' => 'returned',
                'created_at' => Carbon::now(),
            ],
            [
                'user_id' => 3, // Teacher 2
                'book_id' => 8, // Laravel
                'borrow_date' => Carbon::now()->subDays(1),
                'due_date' => Carbon::now()->addDays(6),
                'return_date' => null,
                'status' => 'borrowed',
                'created_at' => Carbon::now(),
            ],
        ]);
    }
}