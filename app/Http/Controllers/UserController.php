<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Borrow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // 3.1. Lấy danh sách Users (Chỉ Admin)
    public function index(Request $request)
    {
        // Yêu cầu: Chỉ admin mới được truy cập [cite: 163]
        $query = User::query()->select('id', 'email', 'full_name', 'phone_number', 'address', 'role', 'created_at'); // Không lấy password 

        // Search: tìm trong email hoặc full_name [cite: 166]
        if ($request->has('search')) {
            $keyword = $request->search;
            $query->where(function($q) use ($keyword) {
                $q->where('email', 'like', "%$keyword%")
                  ->orWhere('full_name', 'like', "%$keyword%");
            });
        }

        // Sorting: ?sort=created_at&order=desc [cite: 165]
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');
        $query->orderBy($sort, $order);

        // Pagination: ?page=1&limit=10 [cite: 164]
        $limit = $request->get('limit', 10);
        $users = $query->paginate($limit);

        return response()->json([
            'success' => true,
            'message' => 'Users fetched successfully',
            'data' => $users
        ], 200);
    }

    // 3.2. Lấy User theo ID
    public function show($id, Request $request)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404); // [cite: 172]
        }

        // Yêu cầu: Admin hoặc chính user đó mới được xem [cite: 171]
        if ($request->user()->role !== 'admin' && $request->user()->id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json(['success' => true, 'data' => $user->makeHidden('password')], 200);
    }

    // 3.3. Cập nhật User
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['success' => false, 'message' => 'User not found'], 404);

        // Yêu cầu: Admin hoặc chính user đó mới được cập nhật [cite: 182]
        if ($request->user()->role !== 'admin' && $request->user()->id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Validate input [cite: 183]
        $validator = Validator::make($request->all(), [
            'full_name' => 'sometimes|string',
            'phone_number' => 'sometimes|string',
            'address' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        // Không cho phép cập nhật email và role trừ khi là admin 
        $data = $request->only(['full_name', 'phone_number', 'address']);
        if ($request->user()->role === 'admin') {
            if ($request->has('email')) $data['email'] = $request->email;
            if ($request->has('role')) $data['role'] = $request->role;
        }

        $user->update($data);

        return response()->json([
            'success' => true, 
            'message' => 'User updated successfully', 
            'data' => $user // [cite: 185]
        ], 200);
    }

    // 3.4. Xóa User (Chỉ Admin)
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['success' => false, 'message' => 'User not found'], 404);

        // Yêu cầu: Chỉ admin mới được xóa [cite: 189]
        // Kiểm tra user có đang mượn sách không (status != "returned") 
        $hasActiveBorrows = Borrow::where('user_id', $id)
            ->where('status', '!=', 'returned')
            ->exists();

        if ($hasActiveBorrows) {
            return response()->json([
                'success' => false, 
                'message' => 'Cannot delete user with active borrows'
            ], 400); // [cite: 191]
        }

        $user->delete(); // [cite: 192]
        return response()->json(['success' => true, 'message' => 'User deleted successfully'], 200);
    }
}