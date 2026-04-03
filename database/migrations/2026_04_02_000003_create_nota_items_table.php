<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Split Tagihan: Satu nota bisa dibagi ke multiple divisi
     * Contoh: Tagihan listrik dibagi ke Aga (60%) + Agri (40%)
     */
    public function up(): void
    {
        Schema::create('nota_items', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('nota_id')->constrained('notas')->onDelete('cascade');
            $table->foreignId('divisi_id')->constrained('divisis')->onDelete('restrict');
            
            $table->unsignedBigInteger('nominal'); // Rp
            $table->decimal('persentase', 5, 2)->nullable(); // % (jika ada)
            
            $table->timestamps();
            
            // Unique constraint: satu nota tidak boleh split ke divisi yg sama 2x
            $table->unique(['nota_id', 'divisi_id']);
            
            $table->index('divisi_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_items');
    }
};
