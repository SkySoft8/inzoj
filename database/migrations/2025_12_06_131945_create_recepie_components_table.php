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
        Schema::create('recepie_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recepie_id')
                ->constrained('recepies')
                ->onDelete('cascade');
            $table->enum('component', ['poultry', 'meat', 'fish', 'vegetables', 'fruits', 'sweet']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recepie_components');
    }
};
