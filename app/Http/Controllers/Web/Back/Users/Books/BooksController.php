<?php

namespace App\Http\Controllers\Web\Back\Users\Books;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Course;
use App\Models\UserBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BooksController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $track = $request->query('track', 'digital-sat');

        $books = Book::query()
            ->with('course')
            ->where('status', 'ready')
            ->whereHas('course', function ($query) use ($user, $track) {
                $query->where('level_id', $user->level_id)
                    ->when($track, function ($query) use ($track) {
                        $query->where('track_slug', $track);
                    });
            })
            ->orderByDesc('created_at')
            ->get();

        $ownedBookIds = UserBook::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->pluck('book_id')
            ->toArray();

        return view('themes/default/back.users.books.books-list', compact(
            'books',
            'ownedBookIds',
            'track'
        ));
    }

    public function show(Request $request, Book $book)
    {
        $user = Auth::user();
        $track = $request->query('track', 'digital-sat');

        if ($book->status !== 'ready') {
            abort(404);
        }

        if (! $this->bookBelongsToStudentCourse($book, $user, $track)) {
            abort(404);
        }

        $userBook = UserBook::query()
            ->where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->first();

        return view('themes/default/back.users.books.book-show', compact(
            'book',
            'userBook',
            'track'
        ));
    }

    public function purchase(Request $request, Book $book)
    {
        $user = Auth::user();
        $track = $request->query('track', 'digital-sat');

        if ($book->status !== 'ready') {
            abort(404);
        }

        if (! $this->bookBelongsToStudentCourse($book, $user, $track)) {
            abort(404);
        }

        $startsAt = now();
        $expiresAt = null;

        if (! empty($book->access_duration_days)) {
            $expiresAt = now()->addDays((int) $book->access_duration_days);
        }

        UserBook::updateOrCreate(
            [
                'user_id' => $user->id,
                'book_id' => $book->id,
            ],
            [
                'source' => 'manual_purchase_test',
                'starts_at' => $startsAt,
                'expires_at' => $expiresAt,
                'is_active' => true,
            ]
        );

        return redirect()
            ->route('books.reader.read', ['book' => $book->slug])
            ->with('success', 'Book purchased successfully.');
    }

    private function bookBelongsToStudentCourse(Book $book, $user, ?string $track): bool
    {
        if (! $book->course_id || ! $user?->level_id) {
            return false;
        }

        return Course::query()
            ->where('id', $book->course_id)
            ->where('level_id', $user->level_id)
            ->when($track, function ($query) use ($track) {
                $query->where('track_slug', $track);
            })
            ->exists();
    }
}
