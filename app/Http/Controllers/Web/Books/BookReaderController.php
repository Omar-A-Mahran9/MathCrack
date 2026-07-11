<?php

namespace App\Http\Controllers\Web\Books;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookPage;
use App\Models\BookPageViewLog;
use App\Models\UserBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class BookReaderController extends Controller
{
    public function read(Book $book)
    {
        $user = Auth::user();

        if (! $this->userCanReadBook($user, $book)) {
            abort(403);
        }

        if ($book->status !== 'ready') {
            abort(404);
        }

        $pages = $book->pages()
            ->orderBy('page_number')
            ->get(['page_number', 'width', 'height']);

        return view('themes/default/front.books.reader', compact('book', 'pages'));
    }

    public function page(Request $request, Book $book, int $page)
    {
        $user = Auth::user();

        if (! $this->userCanReadBook($user, $book)) {
            abort(403);
        }

        if ($book->status !== 'ready') {
            abort(404);
        }

        $bookPage = BookPage::where('book_id', $book->id)
            ->where('page_number', $page)
            ->firstOrFail();

        $disk = Storage::disk('private');

        if (! $disk->exists($bookPage->image_path)) {
            abort(404);
        }

        BookPageViewLog::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'page_number' => $page,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $absolutePath = storage_path('app/private/' . $bookPage->image_path);

        $image = imagecreatefromjpeg($absolutePath);

        if (! $image) {
            abort(500);
        }

        $this->applyWatermark($image, $user);

        ob_start();
        imagejpeg($image, null, 85);
        $content = ob_get_clean();

        imagedestroy($image);

        return Response::make($content, 200, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function userCanReadBook($user, Book $book): bool
    {
        if (! $user) {
            return false;
        }

        /*
         * Admin preview:
         * admins/root users can preview any ready book without being assigned in user_books.
         */
        if (
            $user->roles()->whereIn('name', ['admin', 'root'])->exists()
            || Gate::forUser($user)->allows('show courses')
        ) {
            return true;
        }

        return UserBook::query()
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
            ->exists();
    }

    private function applyWatermark($image, $user): void
    {
        $width = imagesx($image);
        $height = imagesy($image);

        $labelFontPath = resource_path('fonts/watermark.ttf');
        $valueFontPath = resource_path('fonts/watermark.ttf');

        if (! file_exists($labelFontPath)) {
            return;
        }

        if (! file_exists($valueFontPath)) {
            $valueFontPath = $labelFontPath;
        }

        $phone = trim((string) ($user->phone ?? '-'));

        $rows = [
            [
                'label' => 'Name:',
                'value' => trim((string) ($user->name ?? '')),
                'value_font' => $valueFontPath,
            ],
            [
                'label' => 'Email:',
                'value' => trim((string) ($user->email ?? '')),
                'value_font' => $valueFontPath,
            ],
            [
                'label' => 'Phone:',
                'value' => $phone !== '' ? $phone : '-',
                'value_font' => $valueFontPath,
            ],
        ];

        $fontSize = 18;
        $rowSpacing = 12;

        $paddingX = 20;
        $paddingY = 15;
        $radius = 14;

        $labelValueGap = 18;

        $labelWidths = [];
        $valueWidths = [];
        $lineHeights = [];

        foreach ($rows as $row) {
            $labelWidths[] = $this->getTtfTextWidth($row['label'], $labelFontPath, $fontSize);
            $valueWidths[] = $this->getTtfTextWidth($row['value'], $row['value_font'], $fontSize);

            $lineHeights[] = max(
                $this->getTtfTextHeight($labelFontPath, $fontSize),
                $this->getTtfTextHeight($row['value_font'], $fontSize)
            );
        }

        $maxLabelWidth = max($labelWidths);
        $maxValueWidth = max($valueWidths);
        $lineHeight = max($lineHeights);

        $boxWidth = $paddingX + $maxLabelWidth + $labelValueGap + $maxValueWidth + $paddingX;
        $boxHeight = ($paddingY * 2) + (count($rows) * $lineHeight) + ((count($rows) - 1) * $rowSpacing);

        $rightInset = 95;
        $bottomInset = 125;

        $x2 = $width - $rightInset;
        $x1 = $x2 - $boxWidth;

        $y2 = $height - $bottomInset;
        $y1 = $y2 - $boxHeight;

        $x1 = max(10, $x1);
        $y1 = max(10, $y1);
        $x2 = min($width - 10, $x2);
        $y2 = min($height - 10, $y2);

        $backgroundColor = imagecolorallocatealpha($image, 255, 255, 255, 8);
        $borderColor = imagecolorallocatealpha($image, 35, 35, 35, 0);

        $labelColor = imagecolorallocatealpha($image, 38, 88, 150, 0);
        $valueColor = imagecolorallocatealpha($image, 20, 20, 20, 0);

        $this->drawRoundedFilledRectangle($image, $x1, $y1, $x2, $y2, $radius, $backgroundColor);
        $this->drawRoundedRectangleBorder($image, $x1, $y1, $x2, $y2, $radius, $borderColor);

        $labelX = $x1 + $paddingX;
        $valueX = $labelX + $maxLabelWidth + $labelValueGap;

        $baselineY = $y1 + $paddingY + $lineHeight;

        foreach ($rows as $index => $row) {
            $y = $baselineY + ($index * ($lineHeight + $rowSpacing));

            imagettftext($image, $fontSize, 0, $labelX, $y, $labelColor, $labelFontPath, $row['label']);
            imagettftext($image, $fontSize, 0, $valueX, $y, $valueColor, $row['value_font'], $row['value']);
        }
    }

    private function getTtfTextWidth(string $text, string $fontPath, int $fontSize): int
    {
        $box = imagettfbbox($fontSize, 0, $fontPath, $text);

        return abs($box[2] - $box[0]);
    }

    private function getTtfTextHeight(string $fontPath, int $fontSize): int
    {
        $box = imagettfbbox($fontSize, 0, $fontPath, 'Ag');

        return abs($box[7] - $box[1]);
    }

    private function drawRoundedFilledRectangle($image, int $x1, int $y1, int $x2, int $y2, int $radius, int $color): void
    {
        imagefilledrectangle($image, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
        imagefilledrectangle($image, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);

        imagefilledellipse($image, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    }

    private function drawRoundedRectangleBorder($image, int $x1, int $y1, int $x2, int $y2, int $radius, int $color): void
    {
        imageline($image, $x1 + $radius, $y1, $x2 - $radius, $y1, $color);
        imageline($image, $x1 + $radius, $y2, $x2 - $radius, $y2, $color);
        imageline($image, $x1, $y1 + $radius, $x1, $y2 - $radius, $color);
        imageline($image, $x2, $y1 + $radius, $x2, $y2 - $radius, $color);

        imagearc($image, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, 180, 270, $color);
        imagearc($image, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, 270, 360, $color);
        imagearc($image, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, 90, 180, $color);
        imagearc($image, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, 0, 90, $color);
    }
}