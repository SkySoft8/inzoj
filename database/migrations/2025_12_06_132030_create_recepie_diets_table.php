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
        Schema::create('recepie_diets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recepie_id')
                ->constrained('recepies')
                ->onDelete('cascade');
            $table->enum('diet', ['vegetarian', 'vegan', 'low_fat', 'lots_of_fiber', 'low_carb', 'keto_diet', 'high_protein', 'lactose_free']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recepie_diets');
    }
};
