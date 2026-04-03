<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Master Divisi/Lokasi
     * Contoh: Aga, Agri, CLS Q, LL Q, Marketing, IT
     */
    public function up(): void
    {
        Schema::create('divisis', function (Blueprint $table) {
            $table->id();
            
            $table->string('nama')->unique();
            $table->text('deskripsi')->nullable();
            $table->boolean('aktif')->default(true);
            
            $table->timestamps();
            
            $table->index('aktif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('divisis');
    }
};
