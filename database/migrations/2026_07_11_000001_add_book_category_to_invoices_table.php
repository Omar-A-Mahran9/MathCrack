<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $this->ensureCategoryValues(['quiz', 'lecture', 'live', 'book']);
    }

    public function down()
    {
        DB::table('invoices')
            ->where('category', 'book')
            ->update(['category' => 'lecture']);

        $this->ensureCategoryValues(['quiz', 'lecture', 'live']);
    }

    private function ensureCategoryValues(array $values): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $enumValues = "'" . implode("', '", $values) . "'";

        DB::statement("ALTER TABLE `invoices` MODIFY `category` ENUM({$enumValues}) NOT NULL DEFAULT 'lecture'");
    }
};
