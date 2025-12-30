<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BorrowController extends Controller
{
    // 5.1. Mượn Sách [cite: 274]
    public function borrow(Request $request)
    {
        // 1. Validate input [cite: 283]
        $validator = Validator::make($request->all(), [
            'book_id' => 'required|exists:books,id',
            'days_to_borrow' => 'required|integer|between:1,30',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $user = $request->user();
        $book = Book::find($request->book_id);

        // 2. Kiểm tra điều kiện mượn
        // - Sách còn bản copy không [cite: 284]
        if ($book->available_copies <= 0) {
            return response()->json(['success' => false, 'message' => 'Book is currently out of stock'], 400);
        }

        // - User chưa mượn quá 5 sách cùng lúc [cite: 285]
        $activeBorrowsCount = Borrow::where('user_id', $user->id)->where('status', '!=', 'returned')->count();
        if ($activeBorrowsCount >= 5) {
            return response()->json(['success' => false, 'message' => 'You cannot borrow more than 5 books at a time'], 400);
        }

        // - User chưa mượn cùng cuốn sách này [cite: 286]
        $alreadyBorrowed = Borrow::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('status', '!=', 'returned')
            ->exists();
        if ($alreadyBorrowed) {
            return response()->json(['success' => false, 'message' => 'You are already borrowing this book'], 400);
        }

        // 3. Thực hiện Transaction [cite: 374]
        DB::beginTransaction();
        try {
            // Tạo record borrows [cite: 287-290]
            $borrow = Borrow::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'borrow_date' => Carbon::now(),
                'due_date' => Carbon::now()->addDays($request->days_to_borrow),
                'status' => 'borrowed'
            ]);

            // Giảm available_copies đi 1 [cite: 291]
            $book->decrement('available_copies');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Book borrowed successfully',
                'data' => $borrow->load('book') // Include thông tin book [cite: 331]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback nếu có lỗi [cite: 376]
            return response()->json(['success' => false, 'message' => 'Could not process borrowing'], 500);
        }
    }

    // 5.2. Trả Sách [cite: 314]
    public function returnBook($id, Request $request)
    {
        $borrow = Borrow::find($id);

        // Kiểm tra tồn tại và trạng thái [cite: 319]
        if (!$borrow || $borrow->status === 'returned') {
            return response()->json(['success' => false, 'message' => 'Invalid borrow record or already returned'], 400);
        }

        // Kiểm tra quyền: Chỉ user mượn hoặc admin mới được trả [cite: 318]
        if ($request->user()->id !== $borrow->user_id && $request->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Thực hiện Transaction [cite: 375]
        DB::beginTransaction();
        try {
            // Cập nhật trạng thái mượn [cite: 321, 322]
            $borrow->update([
                'return_date' => Carbon::now(),
                'status' => 'returned'
            ]);

            // Tăng available_copies lên 1 [cite: 323]
            $book = Book::find($borrow->book_id);
            $book->increment('available_copies');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Book returned successfully',
                'data' => $borrow
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Could not process return'], 500);
        }
    }
}