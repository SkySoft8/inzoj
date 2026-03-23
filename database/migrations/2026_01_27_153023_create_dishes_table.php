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
        Schema::create('dishes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')
                ->constrained('restaurants')
                ->onDelete('cascade');
            $table->string('name');
            $table->integer('price');
            $table->integer('weight');
            $table->text('ingredients');
            $table->decimal('calories', 5, 1);
            $table->decimal('proteins', 4, 1);
            $table->decimal('fats', 4, 1);
            $table->decimal('carbs', 4, 1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dishes');
    }
};
