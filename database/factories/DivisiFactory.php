<?php

namespace Database\Factories;

use App\Models\Divisi;
use Illuminate\Database\Eloquent\Factories\Factory;

class DivisiFactory extends Factory
{
    protected $model = Divisi::class;

    public function definition(): array
    {
        return [
            'nama' => $this->faker->unique()->word(),
            'deskripsi' => $this->faker->sentence(),
            'aktif' => true,
        ];
    }

    public function inactive(): self
    {
        return $this->state([
            'aktif' => false,
        ]);
    }
}
