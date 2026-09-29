<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('serving_label')->nullable()->after('carbs');
            $table->unsignedInteger('serving_grams')->nullable()->after('serving_label');
        });

        Schema::table('user_recepies', function (Blueprint $table) {
            $table->json('meal_types')->nullable();
            $table->json('components')->nullable();
            $table->json('cooking_methods')->nullable();
            $table->json('diets')->nullable();
        });

        Schema::create('user_favorite_user_recepies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_recepie_id')->constrained('user_recepies')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'user_recepie_id']);
        });

        Schema::create('user_filter_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('filter_group', 32);
            $table->string('filter_value', 32);
            $table->unsignedInteger('hits')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'filter_group', 'filter_value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_filter_views');
        Schema::dropIfExists('user_favorite_user_recepies');

        Schema::table('user_recepies', function (Blueprint $table) {
            $table->dropColumn(['meal_types', 'components', 'cooking_methods', 'diets']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['serving_label', 'serving_grams']);
        });
    }
};
