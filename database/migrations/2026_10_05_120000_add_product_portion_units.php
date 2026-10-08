<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('serving_unit', 8)->nullable()->after('serving_grams');
        });

        Schema::table('user_meals', function (Blueprint $table) {
            $table->unsignedInteger('portion_quantity')->nullable()->after('amount');
            $table->string('portion_unit', 8)->nullable()->after('portion_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('user_meals', function (Blueprint $table) {
            $table->dropColumn(['portion_quantity', 'portion_unit']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('serving_unit');
        });
    }
};
