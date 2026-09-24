<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_recepies', function (Blueprint $table) {
            $table->unsignedSmallInteger('portions')->default(1)->after('carbs');
            $table->string('image')->nullable()->after('instructions');
            $table->json('steps')->nullable()->after('instructions');
        });

        Schema::create('user_recepie_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_recepie_id')
                ->constrained('user_recepies')
                ->cascadeOnDelete();
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();
            $table->unsignedInteger('grams');
            $table->timestamps();
        });

        Schema::table('user_meals', function (Blueprint $table) {
            $table->foreignId('user_recepie_id')
                ->nullable()
                ->after('recepie_id')
                ->constrained('user_recepies')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('user_meals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_recepie_id');
        });

        Schema::dropIfExists('user_recepie_items');

        Schema::table('user_recepies', function (Blueprint $table) {
            $table->dropColumn(['portions', 'image', 'steps']);
        });
    }
};
