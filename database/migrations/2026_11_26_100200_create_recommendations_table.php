<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->text('text');
            $table->enum('severity', ['info', 'warning', 'critical'])->default('info');
            $table->string('metric', 32);
            $table->string('nutrient_code')->nullable();
            $table->string('goal', 32)->nullable();
            $table->string('comparison', 8);
            $table->decimal('threshold', 10, 2)->nullable();
            $table->decimal('threshold_max', 10, 2)->nullable();
            $table->string('value_unit', 16)->nullable();
            $table->enum('period', ['day', 'week', 'month', 'period'])->default('period');
            $table->unsignedTinyInteger('min_tracked_days')->default(0);
            $table->decimal('min_coverage', 5, 2)->default(0);
            $table->smallInteger('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};
