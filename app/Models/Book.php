<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'description',
        'course_id',
        'price',
        'access_duration_days',
        'allow_print',
        'original_pdf_path',
        'total_pages',
        'status',
        'created_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'access_duration_days' => 'integer',
        'allow_print' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function pages()
    {
        return $this->hasMany(BookPage::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_books')
            ->withPivot([
                'source',
                'starts_at',
                'expires_at',
                'is_active',
            ])
            ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isReady(): bool
    {
        return $this->status === 'ready';
    }

    public function hasLimitedAccessDuration(): bool
    {
        return ! is_null($this->access_duration_days) && $this->access_duration_days > 0;
    }

    public function getAccessDurationTextAttribute(): string
    {
        if (! $this->hasLimitedAccessDuration()) {
            return 'Lifetime';
        }

        return $this->access_duration_days . ' days';
    }
}
