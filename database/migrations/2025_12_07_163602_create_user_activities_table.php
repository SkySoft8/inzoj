<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('diary_note_id')
                ->constrained('diary_notes')
                ->onDelete('cascade');
            $table->foreignId('activity_id')
                ->constrained('activities')
                ->onDelete('cascade');
            $table->integer('time_count');
            $table->enum('time_type', ['minute', 'hour'])->default('minute');
            $table->integer('calories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
