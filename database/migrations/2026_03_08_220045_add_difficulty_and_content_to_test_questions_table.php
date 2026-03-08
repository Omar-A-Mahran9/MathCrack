<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_questions', function (Blueprint $table) {
            $table->string('difficulty')->nullable()->after('score');
            $table->string('content')->nullable()->after('difficulty');
        });
    }

    public function down(): void
    {
        Schema::table('test_questions', function (Blueprint $table) {
            $table->dropColumn(['difficulty', 'content']);
        });
    }
};