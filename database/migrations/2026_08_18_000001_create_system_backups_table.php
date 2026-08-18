<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_backups', function (Blueprint $table) {
            $table->id();
            $table->string('token', 40)->unique();
            $table->string('path');
            $table->string('checksum', 64);
            $table->string('data_fingerprint', 64);
            $table->unsignedBigInteger('file_size');
            $table->unsignedInteger('nota_count')->default(0);
            $table->string('status', 20)->default('ready');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_backups');
    }
};
