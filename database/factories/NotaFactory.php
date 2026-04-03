<?php

namespace Database\Factories;

use App\Models\Nota;
use App\Models\User;
use App\Models\Divisi;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotaFactory extends Factory
{
    protected $model = Nota::class;

    public function definition(): array
    {
        $tipe = $this->faker->randomElement(['biasa', 'split', 'revenue_sharing', 'kelebihan_bayar', 'digital']);
        $tanggal = $this->faker->dateTimeBetween('-3 months')->format('Y-m-d');
        $tahun = date('Y', strtotime($tanggal));
        $bulan = date('m', strtotime($tanggal));
        $nominal = $this->faker->numberBetween(100000, 50000000);

        $data = [
            'user_id' => User::first()?->id ?? User::factory(),
            'divisi_id' => Divisi::first()?->id,
            'tipe' => $tipe,
            'status' => $this->faker->randomElement(['draft', 'pending', 'approved']),
            'nomor_nota' => $this->faker->unique()->numerify('NOT-####-##'),
            'keterangan' => $this->faker->sentence(8),
            'tanggal_nota' => $tanggal,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'nominal' => $nominal,
        ];

        // Tipe-specific fields
        if ($tipe === 'revenue_sharing') {
            $data['base_amount'] = $nominal;
            $data['persentase'] = $this->faker->numberBetween(5, 20);
            $data['nominal'] = (int)($nominal * $data['persentase'] / 100);
        }

        if ($tipe === 'kelebihan_bayar') {
            $data['nominal_seharusnya'] = $nominal;
            $data['nominal_dibayar'] = $nominal + $this->faker->numberBetween(50000, 500000);
            $data['selisih'] = $data['nominal_dibayar'] - $data['nominal_seharusnya'];
            $data['nominal'] = $data['nominal_dibayar'];
        }

        if ($data['status'] === 'approved') {
            $data['approver_id'] = User::first()?->id ?? User::factory();
            $data['approved_at'] = $this->faker->dateTimeBetween('-10 days')->format('Y-m-d H:i:s');
        }

        return $data;
    }

    /**
     * State untuk draft nota
     */
    public function draft(): self
    {
        return $this->state([
            'status' => 'draft',
        ]);
    }

    /**
     * State untuk pending review
     */
    public function pending(): self
    {
        return $this->state([
            'status' => 'pending',
        ]);
    }

    /**
     * State untuk approved nota
     */
    public function approved(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
                'approver_id' => User::first()?->id ?? User::factory(),
                'approved_at' => $this->faker->dateTimeBetween('-10 days')->format('Y-m-d H:i:s'),
            ];
        });
    }

    /**
     * State untuk tipe split tagihan
     */
    public function split(): self
    {
        return $this->state([
            'tipe' => 'split',
        ]);
    }

    /**
     * State untuk tipe revenue sharing
     */
    public function revenueSharing(): self
    {
        return $this->state(function (array $attributes) {
            $baseAmount = $this->faker->numberBetween(100000, 50000000);
            $persentase = $this->faker->numberBetween(5, 20);
            return [
                'tipe' => 'revenue_sharing',
                'base_amount' => $baseAmount,
                'persentase' => $persentase,
                'nominal' => (int)($baseAmount * $persentase / 100),
            ];
        });
    }

    /**
     * State untuk tipe kelebihan bayar
     */
    public function kelebihanBayar(): self
    {
        return $this->state(function (array $attributes) {
            $seharusnya = $this->faker->numberBetween(100000, 50000000);
            $dibayar = $seharusnya + $this->faker->numberBetween(50000, 500000);
            return [
                'tipe' => 'kelebihan_bayar',
                'nominal_seharusnya' => $seharusnya,
                'nominal_dibayar' => $dibayar,
                'selisih' => $dibayar - $seharusnya,
                'nominal' => $dibayar,
            ];
        });
    }
}
