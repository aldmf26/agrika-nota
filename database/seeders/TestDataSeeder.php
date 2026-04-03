<?php

namespace Database\Seeders;

use App\Models\Nota;
use App\Models\Divisi;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Seed data untuk testing
     * Run: php artisan db:seed --class=TestDataSeeder
     */
    public function run(): void
    {
        // Pastikan divisi sudah ada
        if (Divisi::count() === 0) {
            $this->call(DivisiSeeder::class);
        }

        // Buat 10 nota biasa dalam draft
        Nota::factory(10)->draft()->create();

        // Buat 5 nota pending
        Nota::factory(5)->pending()->create();

        // Buat 8 nota approved
        Nota::factory(8)->state(['status' => 'approved'])->create();

        // Buat 3 nota split tagihan
        Nota::factory(3)->split()->pending()->create();

        // Buat 2 nota revenue sharing
        Nota::factory(2)->revenueSharing()->approved()->create();

        // Buat 2 nota kelebihan bayar
        Nota::factory(2)->kelebihanBayar()->approved()->create();
    }
}
