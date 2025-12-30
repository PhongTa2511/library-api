<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    // 4.1. Lấy danh sách Books (Public)
    public function index(Request $request)
    {
        $query = Book::query();

        // Search: tìm trong title, author, isbn [cite: 209]
        if ($request->has('search')) {
            $keyword = $request->search;
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'like', "%$keyword%")
                  ->orWhere('author', 'like', "%$keyword%")
                  ->orWhere('isbn', 'like', "%$keyword%");
            });
        }

        // Filter by category [cite: 210]
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Sorting [cite: 208]
        $sort = $request->get('sort', 'title');
        $order = $request->get('order', 'asc');
        $query->orderBy($sort, $order);

        // Pagination 
        $limit = $request->get('limit', 10);
        $books = $query->paginate($limit);

        return response()->json([
            'success' => true,
            'message' => 'Books fetched successfully',
            'data' => $books
        ], 200);
    }

    // 4.2. Lấy Book theo ID (Public) [cite: 213]
    public function show($id)
    {
        $book = Book::find($id);
        if (!$book) {
            return response()->json(['success' => false, 'message' => 'Book not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $book], 200);
    }

    // 4.3. Thêm Book (Admin only) [cite: 218]
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'author' => 'required|string',
            'isbn' => 'required|string|unique:books,isbn',
            'category' => 'required|in:Novel,Science,History,Technology',
            'total_copies' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        // Khi tạo, available_copies = total_copies [cite: 236]
        $data = $request->all();
        $data['available_copies'] = $request->total_copies;
        $book = Book::create($data);

        return response()->json(['success' => true, 'message' => 'Book created', 'data' => $book], 201);
    }

    // 4.4. Cập nhật Book (Admin only) [cite: 239]
    public function update(Request $request, $id)
    {
        $book = Book::find($id);
        if (!$book) return response()->json(['success' => false, 'message' => 'Book not found'], 404);

        // Tính toán available_copies khi thay đổi total_copies [cite: 248-251]
        if ($request->has('total_copies')) {
            $new_total = $request->total_copies;
            $diff = $new_total - $book->total_copies;
            $new_available = $book->available_copies + $diff;

            if ($new_available < 0) {
                return response()->json(['success' => false, 'message' => 'New total copies too low for active borrows'], 400);
            }
            $book->available_copies = $new_available;
            $book->total_copies = $new_total;
        }

        $book->update($request->except('total_copies', 'available_copies'));
        $book->save();

        return response()->json(['success' => true, 'message' => 'Book updated', 'data' => $book], 200);
    }

    // 4.5. Xóa Book (Admin only) [cite: 254]
    public function destroy($id)
    {
        $book = Book::find($id);
        if (!$book) return response()->json(['success' => false, 'message' => 'Book not found'], 404);

        // Kiểm tra sách có đang được mượn không 
        $activeBorrows = Borrow::where('book_id', $id)->where('status', '!=', 'returned')->exists();
        if ($activeBorrows) {
            return response()->json([
                'success' => false, 
                'message' => 'Cannot delete book with active borrows'
            ], 400); // [cite: 258]
        }

        $book->delete();
        return response()->json(['success' => true, 'message' => 'Book deleted'], 200);
    }
}