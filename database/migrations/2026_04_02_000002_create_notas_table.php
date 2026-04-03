<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            
            // Relasi
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('divisi_id')->nullable()->constrained('divisis')->onDelete('set null');
            $table->foreignId('approver_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Data Nota
            $table->enum('tipe', ['biasa', 'split', 'revenue_sharing', 'kelebihan_bayar', 'digital'])->default('biasa');
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'void'])->default('draft');
            $table->string('nomor_nota')->nullable()->unique();
            $table->text('keterangan');
            
            // Tanggal
            $table->date('tanggal_nota');
            $table->year('tahun')->index();
            $table->tinyInteger('bulan')->index();
            
            // Nominal (utama)
            $table->unsignedBigInteger('nominal')->default(0); // Rp
            
            // Khusus tipe revenue_sharing
            $table->unsignedBigInteger('base_amount')->nullable(); // Rp
            $table->decimal('persentase', 5, 2)->nullable(); // %
            
            // Khusus tipe kelebihan_bayar
            $table->unsignedBigInteger('nominal_seharusnya')->nullable(); // Rp
            $table->unsignedBigInteger('nominal_dibayar')->nullable(); // Rp
            $table->unsignedBigInteger('selisih')->nullable(); // Rp (otomatis)
            
            // Catatan Approver
            $table->text('catatan_approver')->nullable();
            
            // Timestamp
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
            
            // Index untuk query cepat
            $table->index(['user_id', 'status']);
            $table->index(['tipe', 'status']);
            $table->index(['tahun', 'bulan']);
            $table->index('divisi_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
};
