<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\BookPage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Throwable;

class ConvertBookPdfToImages extends Command
{
    private const NO_CONVERTER_MESSAGE = 'PDF conversion tools are not available on this server. Install Poppler/pdftoppm or Ghostscript.';

    protected $signature = 'books:convert-pdf {book_id}';

    protected $description = 'Convert a private PDF book into protected page images.';

    public function handle(): int
    {
        $bookId = (int) $this->argument('book_id');

        $book = Book::find($bookId);

        if (! $book) {
            $this->error("Book not found: {$bookId}");
            return self::FAILURE;
        }

        if (! $book->original_pdf_path) {
            $this->error('Book has no original PDF path.');
            return self::FAILURE;
        }

        $disk = Storage::disk('private');

        if (! $disk->exists($book->original_pdf_path)) {
            $this->error('Original PDF file does not exist in private storage.');
            return self::FAILURE;
        }

        $book->update([
            'status' => 'processing',
            'total_pages' => 0,
        ]);

        $baseDirectory = "books/{$book->uuid}";
        $pagesDirectoryRelative = "{$baseDirectory}/pages";

        $disk->deleteDirectory($pagesDirectoryRelative);
        $disk->makeDirectory($pagesDirectoryRelative);
        BookPage::where('book_id', $book->id)->delete();

        $pdfAbsolutePath = storage_path("app/private/{$book->original_pdf_path}");
        $pagesAbsoluteDirectory = storage_path("app/private/{$pagesDirectoryRelative}");

        $converter = $this->resolveConverter();

        if (! $converter) {
            $book->update(['status' => 'failed']);

            $this->logConversion('error', self::NO_CONVERTER_MESSAGE, [
                'book_id' => $book->id,
            ]);

            $this->error(self::NO_CONVERTER_MESSAGE);

            return self::FAILURE;
        }

        $this->info('Converting PDF to images...');
        $this->info('Using PDF converter: ' . $converter['name']);

        $this->logConversion('info', 'Book PDF conversion started', [
            'book_id' => $book->id,
            'converter' => $converter['name'],
            'binary' => $converter['binary'],
        ]);

        $process = new Process($this->conversionCommand(
            $converter,
            $pdfAbsolutePath,
            $pagesAbsoluteDirectory
        ));

        $process->setTimeout(600);
        $process->run();

        if (! $process->isSuccessful()) {
            $book->update(['status' => 'failed']);

            $this->error('PDF conversion failed.');
            $this->error($process->getErrorOutput());

            return self::FAILURE;
        }

        $generatedImages = $this->generatedImages($pagesAbsoluteDirectory, $converter['name']);

        if (! $generatedImages) {
            $book->update(['status' => 'failed']);

            $this->error('No page images were generated.');

            return self::FAILURE;
        }

        usort($generatedImages, function ($a, $b) {
            return $this->extractPageNumber($a) <=> $this->extractPageNumber($b);
        });

        $pageNumber = 1;

        foreach ($generatedImages as $imagePath) {
            $finalFilename = 'page-' . str_pad((string) $pageNumber, 4, '0', STR_PAD_LEFT) . '.jpg';
            $finalAbsolutePath = $pagesAbsoluteDirectory . DIRECTORY_SEPARATOR . $finalFilename;

            if ($this->normalizePath($imagePath) !== $this->normalizePath($finalAbsolutePath)) {
                rename($imagePath, $finalAbsolutePath);
            }

            $dimensions = @getimagesize($finalAbsolutePath);

            BookPage::create([
                'book_id' => $book->id,
                'page_number' => $pageNumber,
                'image_path' => "{$pagesDirectoryRelative}/{$finalFilename}",
                'width' => $dimensions[0] ?? null,
                'height' => $dimensions[1] ?? null,
            ]);

            $this->line("Page {$pageNumber} created.");

            $pageNumber++;
        }

        $totalPages = $pageNumber - 1;

        $book->update([
            'total_pages' => $totalPages,
            'status' => 'ready',
        ]);

        $this->info("Done. Total pages: {$totalPages}");

        return self::SUCCESS;
    }

    private function resolveConverter(): ?array
    {
        foreach (['pdftoppm', '/usr/bin/pdftoppm', '/usr/local/bin/pdftoppm'] as $binary) {
            if ($this->binaryWorks($binary, ['-v'])) {
                return [
                    'name' => 'pdftoppm',
                    'binary' => $binary,
                ];
            }
        }

        foreach (['gs', '/usr/bin/gs', '/usr/local/bin/gs'] as $binary) {
            if ($this->binaryWorks($binary, ['-v'])) {
                return [
                    'name' => 'ghostscript',
                    'binary' => $binary,
                ];
            }
        }

        return null;
    }

    private function binaryWorks(string $binary, array $arguments): bool
    {
        try {
            $process = new Process(array_merge([$binary], $arguments));
            $process->setTimeout(10);
            $process->run();

            return $process->isSuccessful();
        } catch (Throwable) {
            return false;
        }
    }

    private function conversionCommand(array $converter, string $pdfAbsolutePath, string $pagesAbsoluteDirectory): array
    {
        if ($converter['name'] === 'ghostscript') {
            return [
                $converter['binary'],
                '-dSAFER',
                '-dBATCH',
                '-dNOPAUSE',
                '-sDEVICE=jpeg',
                '-r150',
                '-dJPEGQ=85',
                '-sOutputFile=' . $pagesAbsoluteDirectory . DIRECTORY_SEPARATOR . 'page-%04d.jpg',
                $pdfAbsolutePath,
            ];
        }

        return [
            $converter['binary'],
            '-jpeg',
            '-r',
            '150',
            '-jpegopt',
            'quality=85',
            $pdfAbsolutePath,
            $pagesAbsoluteDirectory . DIRECTORY_SEPARATOR . 'temp-page',
        ];
    }

    private function generatedImages(string $pagesAbsoluteDirectory, string $converterName): array
    {
        $pattern = $converterName === 'ghostscript'
            ? 'page-*.jpg'
            : 'temp-page-*.jpg';

        return glob($pagesAbsoluteDirectory . DIRECTORY_SEPARATOR . $pattern) ?: [];
    }

    private function extractPageNumber(string $path): int
    {
        if (preg_match('/(?:temp-page|page)-(\d+)\.jpg$/', basename($path), $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }

    private function normalizePath(string $path): string
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }

    private function logConversion(string $level, string $message, array $context = []): void
    {
        try {
            Log::log($level, $message, $context);
        } catch (Throwable) {
            // Conversion should still return a clear console/admin error if logging is unavailable.
        }
    }
}
