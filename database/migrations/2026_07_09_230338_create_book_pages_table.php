<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_pages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('book_id')
                ->constrained('books')
                ->cascadeOnDelete();

            $table->unsignedInteger('page_number');

            $table->string('image_path');

            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();

            $table->timestamps();

            $table->unique(['book_id', 'page_number']);
            $table->index(['book_id', 'page_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_pages');
    }
};
