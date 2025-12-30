<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 2.1. Đăng ký User [cite: 84]
    public function register(Request $request)
    {
        // Validate input [cite: 96]
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'full_name' => 'required|string',
            'role' => 'nullable|in:student,teacher,admin'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation fail',
                'errors' => $validator->errors()
            ], 400); // Trả về 400 nếu validation fail [cite: 100]
        }

        // Kiểm tra email tồn tại [cite: 98]
        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Email already exists'
            ], 409); // Trả về 409 nếu email tồn tại [cite: 101]
        }

        // Tạo user mới và hash password 
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'full_name' => $request->full_name,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'role' => $request->role ?? 'student',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => $user // Không bao gồm password vì đã ẩn trong Model [cite: 99]
        ], 201);
    }

    // 2.2. Đăng nhập [cite: 116]
    public function login(Request $request)
    {
        // Validate email và password [cite: 127]
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Kiểm tra email và Verify password [cite: 128, 129]
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Tạo JWT token (Sanctum) 
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'full_name' => $user->full_name,
                    'role' => $user->role
                ]
            ]
        ], 200);
    }

    // 3.5. Lấy thông tin User hiện tại [cite: 196]
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'User profile fetched successfully',
            'data' => $request->user() // Lấy từ token [cite: 199, 200]
        ]);
    }
}