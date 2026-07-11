<?php

namespace App\Http\Controllers\Web\Back\Admins\Books;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookPage;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class BooksController extends Controller
{
    public function index(Request $request)
    {
        if (! Gate::allows('show courses')) {
            return view('themes/default/back.permission-denied');
        }

        if ($request->ajax()) {
            $books = Book::query()
                ->with(['course.level'])
                ->select([
                    'id',
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
                    'created_at',
                ]);

            return DataTables::of($books)
                ->addIndexColumn()
                ->addColumn('course', function ($row) {
                    if (! $row->course) {
                        return '-';
                    }

                    $parts = [$row->course->name];

                    if (! empty($row->course->track_slug)) {
                        $parts[] = $row->course->track_slug;
                    }

                    if ($row->course->level) {
                        $parts[] = $row->course->level->name;
                    }

                    return implode(' | ', array_filter($parts));
                })
                ->editColumn('price', function ($row) {
                    return number_format((float) $row->price, 2);
                })
                ->addColumn('access_duration', function ($row) {
                    if (empty($row->access_duration_days)) {
                        return 'Lifetime';
                    }

                    return $row->access_duration_days . ' days';
                })
                ->editColumn('allow_print', function ($row) {
                    if ($row->allow_print) {
                        return '<span class="badge bg-success">Allowed</span>';
                    }

                    return '<span class="badge bg-secondary">Not Allowed</span>';
                })
                ->editColumn('status', function ($row) {
                    return match ($row->status) {
                        'ready' => '<span class="badge bg-success">Ready</span>',
                        'processing' => '<span class="badge bg-info">Processing</span>',
                        'failed' => '<span class="badge bg-danger">Failed</span>',
                        'disabled' => '<span class="badge bg-secondary">Disabled</span>',
                        default => '<span class="badge bg-warning">Draft</span>',
                    };
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i') : '-';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('dashboard.admins.books-edit', ['id' => encrypt($row->id)]);
                    $convertUrl = route('dashboard.admins.books-convert', ['id' => encrypt($row->id)]);
                    $deleteUrl = route('dashboard.admins.books-delete', ['id' => encrypt($row->id)]);

                    $buttons = '<div class="book-actions" style="display:flex; gap:4px; flex-wrap:nowrap; align-items:center;">';

                    $buttons .= '<a href="' . $editUrl . '" class="btn btn-sm btn-primary" style="min-width:70px; padding:4px 8px; font-size:11px;">Edit</a>';

                    if ($row->status === 'ready') {
                        $readUrl = route('books.reader.read', ['book' => $row->slug]);

                        $buttons .= '<a href="' . $readUrl . '" target="_blank" class="btn btn-sm btn-success" style="min-width:70px; padding:4px 8px; font-size:11px;">Read</a>';
                        $convertText = 'Reconvert';
                    } else {
                        $convertText = 'Convert';
                    }

                    if ($row->status !== 'processing') {
                        $buttons .= '<form action="' . $convertUrl . '" method="POST" style="display:inline-block; margin:0;">'
                            . csrf_field()
                            . '<button type="submit" class="btn btn-sm btn-warning" style="min-width:70px; padding:4px 8px; font-size:11px;" onclick="return confirm(\'Convert this book PDF to images?\')">' . $convertText . '</button>'
                            . '</form>';
                    }

                    $buttons .= '<form action="' . $deleteUrl . '" method="POST" style="display:inline-block; margin:0;" onsubmit="return confirm(\'Delete this book?\')">'
                        . csrf_field()
                        . method_field('DELETE')
                        . '<button type="submit" class="btn btn-sm btn-danger" style="min-width:70px; padding:4px 8px; font-size:11px;">Delete</button>'
                        . '</form>';

                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns([
                    'status',
                    'allow_print',
                    'action',
                ])
                ->make(true);
        }

        $courses = Course::query()
            ->with('level')
            ->orderBy('track_slug')
            ->orderBy('name')
            ->get();

        return view('themes/default/back.admins.books.books-list', compact('courses'));
    }

    public function store(Request $request)
    {
        if (! Gate::allows('add courses')) {
            return view('themes/default/back.permission-denied');
        }

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0|max:99999999.99',
            'access_duration_days' => 'nullable|integer|min:1|max:3650',
            'allow_print' => 'nullable|boolean',
            'pdf' => 'required|file|mimes:pdf|max:102400',
        ]);

        $uuid = (string) Str::uuid();
        $slug = $this->makeUniqueSlug($request->title);
        $path = "books/{$uuid}/original/book.pdf";

        Storage::disk('private')->putFileAs(
            "books/{$uuid}/original",
            $request->file('pdf'),
            'book.pdf'
        );

        Book::create([
            'uuid' => $uuid,
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'course_id' => $request->course_id,
            'price' => $request->price,
            'access_duration_days' => $request->access_duration_days,
            'allow_print' => $request->boolean('allow_print'),
            'original_pdf_path' => $path,
            'total_pages' => 0,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Book uploaded successfully.');
    }

    public function edit(Request $request)
    {
        if (! Gate::allows('edit courses')) {
            return view('themes/default/back.permission-denied');
        }

        $request->validate([
            'id' => 'required',
        ]);

        $book = Book::findOrFail(decrypt($request->id));

        $courses = Course::query()
            ->with('level')
            ->orderBy('track_slug')
            ->orderBy('name')
            ->get();

        return view('themes/default/back.admins.books.books-edit', compact('book', 'courses'));
    }

    public function update(Request $request)
    {
        if (! Gate::allows('edit courses')) {
            return view('themes/default/back.permission-denied');
        }

        $request->validate([
            'id' => 'required',
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0|max:99999999.99',
            'access_duration_days' => 'nullable|integer|min:1|max:3650',
            'allow_print' => 'nullable|boolean',
            'status' => 'required|in:draft,ready,processing,failed,disabled',
            'pdf' => 'nullable|file|mimes:pdf|max:102400',
        ]);

        $book = Book::findOrFail(decrypt($request->id));

        $book->course_id = $request->course_id;
        $book->title = $request->title;
        $book->slug = $this->makeUniqueSlug($request->title, $book->id);
        $book->description = $request->description;
        $book->price = $request->price;
        $book->access_duration_days = $request->access_duration_days;
        $book->allow_print = $request->boolean('allow_print');
        $book->status = $request->status;

        if ($request->hasFile('pdf')) {
            $path = "books/{$book->uuid}/original/book.pdf";

            Storage::disk('private')->deleteDirectory("books/{$book->uuid}/original");
            Storage::disk('private')->deleteDirectory("books/{$book->uuid}/pages");

            Storage::disk('private')->putFileAs(
                "books/{$book->uuid}/original",
                $request->file('pdf'),
                'book.pdf'
            );

            BookPage::where('book_id', $book->id)->delete();

            $book->original_pdf_path = $path;
            $book->total_pages = 0;
            $book->status = 'draft';
        }

        $book->save();

        return redirect()
            ->route('dashboard.admins.books')
            ->with('success', 'Book updated successfully.');
    }

    public function convert(Request $request)
    {
        if (! Gate::allows('edit courses')) {
            return view('themes/default/back.permission-denied');
        }

        $request->validate([
            'id' => 'required',
        ]);

        $book = Book::findOrFail(decrypt($request->id));

        if (! $book->original_pdf_path) {
            return redirect()
                ->back()
                ->withErrors(['pdf' => 'This book has no PDF file.']);
        }

        if (! Storage::disk('private')->exists($book->original_pdf_path)) {
            return redirect()
                ->back()
                ->withErrors(['pdf' => 'Original PDF file does not exist in private storage.']);
        }

        try {
            $exitCode = Artisan::call('books:convert-pdf', [
                'book_id' => $book->id,
            ]);

            if ($exitCode !== 0) {
                $output = trim(Artisan::output());

                return redirect()
                    ->back()
                    ->withErrors(['convert' => $output !== '' ? $output : 'PDF conversion failed. Check the book status or Laravel logs.']);
            }

            return redirect()
                ->back()
                ->with('success', 'Book converted successfully.');
        } catch (Throwable $exception) {
            $book->update(['status' => 'failed']);

            return redirect()
                ->back()
                ->withErrors(['convert' => $exception->getMessage()]);
        }
    }

    public function delete(Request $request)
    {
        if (! Gate::allows('delete courses')) {
            return view('themes/default/back.permission-denied');
        }

        $book = Book::findOrFail(decrypt($request->id));

        Storage::disk('private')->deleteDirectory("books/{$book->uuid}");

        $book->delete();

        return redirect()->back()->with('success', 'Book deleted successfully.');
    }

    private function makeUniqueSlug(string $title, ?int $ignoreBookId = null): string
    {
        $baseSlug = Str::slug($title);

        if ($baseSlug === '') {
            $baseSlug = 'book';
        }

        $slug = $baseSlug;
        $counter = 2;

        while (
            Book::where('slug', $slug)
                ->when($ignoreBookId, function ($query) use ($ignoreBookId) {
                    $query->where('id', '!=', $ignoreBookId);
                })
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
