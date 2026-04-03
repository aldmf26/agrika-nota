<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nota_attachments', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('nota_id')->constrained('notas')->onDelete('cascade');
            
            $table->string('file_name'); // Nama asli
            $table->string('file_path'); // Path storage
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size'); // bytes
            
            $table->timestamps();
            
            $table->index('nota_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_attachments');
    }
};
