<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nutrient_norms', function (Blueprint $table) {
            $table->id();
            $table->string('nutrient_code');
            $table->string('gender', 10)->nullable();
            $table->unsignedTinyInteger('age_min')->nullable();
            $table->unsignedTinyInteger('age_max')->nullable();
            $table->decimal('amount', 12, 3);
            $table->string('source')->nullable();
            $table->timestamps();

            $table->index(['nutrient_code', 'gender']);
            $table->foreign('nutrient_code')
                ->references('code')
                ->on('nutrients')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nutrient_norms');
    }
};
