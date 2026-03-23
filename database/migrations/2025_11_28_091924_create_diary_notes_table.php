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
        Schema::create('diary_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->timestamp('diary_date')->nullable()->useCurrent();
            $table->decimal('current_calories', 5, 1)->nullable()->default(0);
            $table->decimal('burned_calories', 5, 1)->nullable()->default(0);
            $table->decimal('current_proteins', 4, 1)->nullable()->default(0);
            $table->decimal('current_fats', 4, 1)->nullable()->default(0);
            $table->decimal('current_carbs', 4, 1)->nullable()->default(0);
            $table->decimal('current_water', 2, 1)->nullable()->default(0.0);
            $table->integer('current_steps')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diary_notes');
    }
};
