<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_trainings', function (Blueprint $table) {
            $table->string('status', 16)->default('active')->after('training_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_trainings', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
