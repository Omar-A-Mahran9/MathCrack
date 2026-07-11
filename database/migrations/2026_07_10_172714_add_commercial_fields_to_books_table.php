<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->after('description');
            $table->unsignedInteger('access_duration_days')->nullable()->after('price');
            $table->boolean('allow_print')->default(false)->after('access_duration_days');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'price',
                'access_duration_days',
                'allow_print',
            ]);
        });
    }
};