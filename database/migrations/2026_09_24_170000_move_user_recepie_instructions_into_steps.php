<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('user_recepies', 'instructions')) {
            return;
        }

        foreach (DB::table('user_recepies')->get(['id', 'instructions', 'steps']) as $row) {
            $steps = json_decode($row->steps ?? '', true);
            if (is_array($steps) && count($steps) > 0) {
                continue;
            }

            $legacy = trim((string) ($row->instructions ?? ''));
            if ($legacy === '') {
                continue;
            }

            DB::table('user_recepies')->where('id', $row->id)->update([
                'steps' => json_encode([$legacy], JSON_UNESCAPED_UNICODE),
            ]);
        }

        Schema::table('user_recepies', function (Blueprint $table) {
            $table->dropColumn('instructions');
        });
    }

    public function down(): void
    {
        Schema::table('user_recepies', function (Blueprint $table) {
            $table->text('instructions')->nullable()->after('name');
        });
    }
};
