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
        Schema::create('user_recepie_ingridients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_recepie_id')
                ->constrained('user_recepies')
                ->onDelete('cascade');
            $table->foreignId('ingredient_id')
                ->constrained('recepie_ingredients')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_recepie_ingridients');
    }
};
