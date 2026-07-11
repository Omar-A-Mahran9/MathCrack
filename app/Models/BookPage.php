<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookPage extends Model
{
    protected $fillable = [
        'book_id',
        'page_number',
        'image_path',
        'width',
        'height',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}