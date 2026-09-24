<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_product_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedInteger('times')->default(0);
            $table->timestamp('last_added_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'product_id']);
        });

        $rows = DB::table('user_meals')
            ->whereNotNull('product_id')
            ->selectRaw('user_id, product_id, COUNT(*) as times, MAX(created_at) as last_added_at')
            ->groupBy('user_id', 'product_id')
            ->get();

        foreach ($rows as $row) {
            DB::table('user_product_stats')->insert([
                'user_id' => $row->user_id,
                'product_id' => $row->product_id,
                'times' => $row->times,
                'last_added_at' => $row->last_added_at,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_product_stats');
    }
};
