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
        Schema::table('divisis', function (Blueprint $table) {
            $table->string('kode', 10)->nullable()->unique()->after('id');
        });
        
        // Populate default kode based on nama (take first 3 chars uppercase)
        // Note: It's better to do this in the model or a seeder, 
        // but for a quick fix, users can manually update them.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('divisis', function (Blueprint $table) {
            $table->dropColumn('kode');
        });
    }
};
