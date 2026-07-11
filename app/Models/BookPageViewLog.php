<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookPageViewLog extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'page_number',
        'ip_address',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}