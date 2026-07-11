<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();

            $table->string('title');
            $table->string('slug')->unique();

            $table->text('description')->nullable();

            $table->string('original_pdf_path')->nullable();

            $table->unsignedInteger('total_pages')->default(0);

            $table->string('status', 30)->default('draft');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
