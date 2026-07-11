<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $column = DB::selectOne("
            SELECT COLUMN_TYPE
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'invoices'
                AND COLUMN_NAME = 'category'
        ");

        if (! $column || str_contains((string) $column->COLUMN_TYPE, "'book'")) {
            return;
        }

        DB::statement("ALTER TABLE `invoices` MODIFY `category` ENUM('quiz', 'lecture', 'live', 'book') NOT NULL DEFAULT 'lecture'");
    }

    public function down()
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::table('invoices')
            ->where('category', 'book')
            ->update(['category' => 'lecture']);

        DB::statement("ALTER TABLE `invoices` MODIFY `category` ENUM('quiz', 'lecture', 'live') NOT NULL DEFAULT 'lecture'");
    }
};
