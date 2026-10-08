<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->unsignedInteger('calories')->nullable()->after('price');
        });

        Schema::table('user_activities', function (Blueprint $table) {
            $table->foreignId('training_id')
                ->nullable()
                ->after('activity_id')
                ->constrained('trainings')
                ->nullOnDelete();
            $table->unique(['user_id', 'diary_note_id', 'training_id'], 'user_activities_day_training_unique');
        });

        DB::statement('ALTER TABLE user_activities MODIFY activity_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        Schema::table('user_activities', function (Blueprint $table) {
            $table->dropUnique('user_activities_day_training_unique');
            $table->dropForeign(['training_id']);
            $table->dropColumn('training_id');
        });

        DB::statement('DELETE FROM user_activities WHERE activity_id IS NULL');
        DB::statement('ALTER TABLE user_activities MODIFY activity_id BIGINT UNSIGNED NOT NULL');

        Schema::table('trainings', function (Blueprint $table) {
            $table->dropColumn('calories');
        });
    }
};
