<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\BookPage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class ConvertBookPdfToImages extends Command
{
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

        $pdfAbsolutePath = storage_path("app/private/{$book->original_pdf_path}");
        $pagesAbsoluteDirectory = storage_path("app/private/{$pagesDirectoryRelative}");
        $outputPrefix = $pagesAbsoluteDirectory . DIRECTORY_SEPARATOR . 'temp-page';

        $this->info('Converting PDF to images...');

        $process = new Process([
            'pdftoppm',
            '-jpeg',
            '-r',
            '150',
            '-jpegopt',
            'quality=85',
            $pdfAbsolutePath,
            $outputPrefix,
        ]);

        $process->setTimeout(600);
        $process->run();

        if (! $process->isSuccessful()) {
            $book->update(['status' => 'failed']);

            $this->error('PDF conversion failed.');
            $this->error($process->getErrorOutput());

            return self::FAILURE;
        }

        $generatedImages = glob($pagesAbsoluteDirectory . DIRECTORY_SEPARATOR . 'temp-page-*.jpg');

        if (! $generatedImages) {
            $book->update(['status' => 'failed']);

            $this->error('No page images were generated.');

            return self::FAILURE;
        }

        usort($generatedImages, function ($a, $b) {
            return $this->extractPageNumber($a) <=> $this->extractPageNumber($b);
        });

        BookPage::where('book_id', $book->id)->delete();

        $pageNumber = 1;

        foreach ($generatedImages as $imagePath) {
            $finalFilename = 'page-' . str_pad((string) $pageNumber, 4, '0', STR_PAD_LEFT) . '.jpg';
            $finalAbsolutePath = $pagesAbsoluteDirectory . DIRECTORY_SEPARATOR . $finalFilename;

            rename($imagePath, $finalAbsolutePath);

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

    private function extractPageNumber(string $path): int
    {
        if (preg_match('/temp-page-(\d+)\.jpg$/', $path, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }
}