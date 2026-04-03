<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catat kelebihan bayar (tipe: kelebihan_bayar)
     * Digunakan untuk potong di transaksi supplier yang sama berikutnya
     */
    public function up(): void
    {
        Schema::create('deposit_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('nota_id')->constrained('notas')->onDelete('restrict');
            $table->foreignId('divisi_id')->constrained('divisis')->onDelete('restrict');

            $table->unsignedBigInteger('nominal'); // Rp (selisih)
            $table->enum('status', ['tersedia', 'terpakai', 'void'])->default('tersedia');

            // Referensi nota yang menggunakan deposit ini
            $table->foreignId('dipakai_di_nota_id')->nullable()->constrained('notas')->onDelete('set null');
            $table->timestamp('dipakai_at')->nullable();

            $table->timestamps();

            $table->index(['divisi_id', 'status']);
            $table->index('dipakai_di_nota_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposit_logs');
    }
};
