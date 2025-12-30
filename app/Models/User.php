<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Cần thiết cho Phần 2: Authentication [cite: 130]

class User extends Authenticatable
{
    use  HasApiTokens, Notifiable;

    // Danh sách các trường có thể gán dữ liệu hàng loạt [cite: 15-22]
    protected $fillable = [
        'email',
        'password',
        'full_name',
        'phone_number',
        'address',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Thiết lập mối quan hệ: Một người dùng có thể có nhiều bản ghi mượn sách [cite: 55]
    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }
}