<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'category',
        'description',
        'total_copies',
        'available_copies',
        'published_year',
        'publisher',
    ];

    // Thiết lập mối quan hệ: Một cuốn sách có thể xuất hiện trong nhiều bản ghi mượn [cite: 57]
    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }
}