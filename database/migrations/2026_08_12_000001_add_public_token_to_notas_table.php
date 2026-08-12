<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notas', function (Blueprint $table) {
            $table->string('public_token', 12)->nullable()->unique()->after('nomor_nota');
        });

        DB::table('notas')->orderBy('id')->chunkById(100, function ($notas) {
            foreach ($notas as $nota) {
                do {
                    $token = Str::random(12);
                } while (DB::table('notas')->where('public_token', $token)->exists());

                DB::table('notas')->where('id', $nota->id)->update(['public_token' => $token]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('notas', function (Blueprint $table) {
            $table->dropUnique(['public_token']);
            $table->dropColumn('public_token');
        });
    }
};
