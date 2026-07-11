<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = [];

    protected $with = ['course'];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias for student relationship (for consistency)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function lecture()
    {
        return $this->belongsTo(Lecture::class, 'type_value');
    }

    public function lectureAssignment()
    {
        return $this->belongsTo(LectureAssignment::class, 'type_value');
    }

    public function test()
    {
        return $this->belongsTo(Test::class, 'type_value');
    }

    public function live()
    {
        return $this->belongsTo(Live::class, 'type_value');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'type_value');
    }

    public function getTypeValueDisplayAttribute()
    {
        $courseName = $this->course ? ' (' . $this->course->name . ')' : '';

        if ($this->type === 'course' && $this->category === 'quiz' && $this->course) {
            return __('l.all_tests_for_course', ['course' => $this->course->name]);
        } elseif ($this->type === 'course' && $this->course) {
            return $this->course->name;
        } elseif ($this->type === 'single' && $this->category === 'lecture' && $this->lecture) {
            return $this->lecture->name . $courseName;
        } elseif ($this->type === 'single' && $this->category === 'quiz' && $this->test) {
            return $this->test->name . $courseName;
        } elseif ($this->type === 'single' && $this->category === 'quiz' && $this->lectureAssignment) {
            return $this->lectureAssignment->title . $courseName;
        } elseif ($this->type === 'single' && $this->category === 'live' && $this->live) {
            return $this->live->name . $courseName;
        } elseif ($this->type === 'single' && $this->category === 'book' && $this->book) {
            return $this->book->title . $courseName;
        }
        return $this->type_value . $courseName;
    }

    public function getStatusBadgeAttribute()
    {
        $badgeClass = match($this->status) {
            'pending' => 'bg-warning',
            'paid' => 'bg-success',
            'failed' => 'bg-danger',
            default => 'bg-light'
        };
        return '<span class="badge ' . $badgeClass . '">' . ucfirst($this->status) . '</span>';
    }

    public function getCategoryBadgeAttribute()
    {
        $badgeClass = match($this->category) {
            'quiz' => 'bg-warning',
            'live' => 'bg-danger',
            'book' => 'bg-primary',
            'lecture' => 'bg-info',
            default => 'bg-light'
        };
        return '<span class="badge ' . $badgeClass . '">' . ucfirst($this->category) . '</span>';
    }

    public function getTypeBadgeAttribute()
    {
        $badgeClass = match($this->type) {
            'single' => 'bg-primary',
            'month' => 'bg-success',
            'course' => 'bg-secondary',
            default => 'bg-light'
        };
        return '<span class="badge ' . $badgeClass . '">' . ucfirst($this->type) . '</span>';
    }
}
