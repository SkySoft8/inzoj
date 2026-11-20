<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY activity_level ENUM('low','base','medium','high','expert') NULL");
    }

    public function down(): void
    {
        if (!in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::table('users')->where('activity_level', 'base')->update(['activity_level' => 'low']);
        DB::statement("ALTER TABLE users MODIFY activity_level ENUM('low','medium','high','expert') NULL");
    }
};
