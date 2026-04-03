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
        Schema::create('nota_archives', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('original_id');
            $table->string('nomor_nota')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('divisi_id')->nullable();
            $table->date('tanggal_nota');
            $table->unsignedBigInteger('nominal');
            $table->text('keterangan');
            
            $table->json('full_data'); // Store everything as backup
            $table->string('deleted_by')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_archives');
    }
};
