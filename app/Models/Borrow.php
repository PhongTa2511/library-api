<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'notes',
    ];

    // Thiết lập mối quan hệ: Bản ghi mượn thuộc về một User nhất định [cite: 42, 55]
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Thiết lập mối quan hệ: Bản ghi mượn thuộc về một Book nhất định [cite: 44, 57]
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}