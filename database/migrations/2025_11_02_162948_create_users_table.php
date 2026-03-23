<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('goal', ['lose_weight', 'gain_muscle', 'maintain', 'other'])->nullable();
            $table->integer('current_weight')->nullable();
            $table->integer('target_weight')->nullable();
            $table->integer('height')->nullable();
            $table->integer('age')->nullable();
            $table->enum('gender' , ['male', 'female'])->nullable();
            $table->enum('activity_level', ['low', 'medium', 'high', 'expert'])->nullable();
            $table->integer('calories')->nullable();
            $table->decimal('water', 2, 1)->nullable();
            $table->integer('steps')->nullable();
            $table->enum('food_preferences', ['no_preferences', 'vegetarian', 'vegan'])->default('no_preferences');
            $table->string('allergies')->nullable();
            $table->boolean('is_premium')->default(false);
            $table->timestamp('premium_until')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};