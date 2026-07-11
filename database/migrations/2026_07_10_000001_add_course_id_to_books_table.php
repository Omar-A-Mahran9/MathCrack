<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('books', 'course_id')) {
            Schema::table('books', function (Blueprint $table) {
                $table->foreignId('course_id')
                    ->nullable()
                    ->after('description')
                    ->constrained('courses')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('books', 'course_id')) {
            Schema::table('books', function (Blueprint $table) {
                $table->dropConstrainedForeignId('course_id');
            });
        }
    }
};
