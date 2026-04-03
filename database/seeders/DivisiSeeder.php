<?php

namespace Database\Seeders;

use App\Models\Divisi;
use Illuminate\Database\Seeder;

class DivisiSeeder extends Seeder
{
    public function run(): void
    {
        $divisis = [
            ['nama' => 'Aga', 'kode' => 'AGA', 'deskripsi' => 'Divisi Agribusiness General'],
            ['nama' => 'Agri Cost', 'kode' => 'ARC', 'deskripsi' => 'Divisi Agricultural Cost'],
            ['nama' => 'CLS Q', 'kode' => 'CLS', 'deskripsi' => 'Divisi Cold Storage Q'],
            ['nama' => 'LL Q', 'kode' => 'LLQ', 'deskripsi' => 'Divisi Logistik Q'],
            ['nama' => 'Marketing', 'kode' => 'MKT', 'deskripsi' => 'Divisi Marketing & Sales'],
            ['nama' => 'IT', 'kode' => 'IT', 'deskripsi' => 'Divisi Information Technology'],
            ['nama' => 'Finance', 'kode' => 'FIN', 'deskripsi' => 'Divisi Finance & Accounting'],
            ['nama' => 'HR', 'kode' => 'HR', 'deskripsi' => 'Divisi Human Resources'],
        ];

        foreach ($divisis as $divisi) {
            Divisi::updateOrCreate(
                ['nama' => $divisi['nama']],
                $divisi
            );
        }
    }
}
