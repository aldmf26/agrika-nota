<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi untuk CREATE dan UPDATE nota
 * Prinsip: Hanya field yang relevan berdasarkan tipe nota
 */
class StoreNotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Nota::class);
    }

    public function rules(): array
    {
        $rules = [
            'tipe' => ['required', Rule::in(['biasa', 'split', 'revenue_sharing', 'kelebihan_bayar', 'digital'])],
            'tanggal_nota' => ['required', 'date', 'before_or_equal:today'],
            'divisi_id' => ['required', 'exists:divisis,id'],
            'nomor_nota' => ['nullable'],
            'keterangan' => ['required', 'string', 'min:5', 'max:500'],
            'attachments' => ['required', 'array', 'min:1', 'max:20'],
            'attachments.*' => ['file', 'image', 'max:5120', 'mimes:jpeg,png,heic'],
            'bulan' => ['required', 'integer'],
            'tahun' => ['required', 'integer'],
            'nominal' => ['nullable', 'integer', 'min:0'],
        ];

        // Validasi khusus per tipe nota
        if ($this->input('tipe') === 'split') {
            $rules['nominal_total'] = ['required', 'integer', 'min:2000'];
            $rules['split_items'] = ['required', 'array', 'min:2', 'max:20'];
            $rules['split_items.*.divisi_id'] = ['required', 'integer', 'exists:divisis,id'];
            $rules['split_items.*.nominal'] = ['required', 'integer', 'min:1000'];
        } else {
            // Untuk non-split, split_items harus kosong atau tidak ada
            $rules['split_items'] = ['nullable', 'array', 'max:0'];
        }

        if ($this->input('tipe') === 'revenue_sharing') {
            $rules['base_amount'] = ['required', 'integer', 'min:1000'];
            $rules['persentase'] = ['required', 'numeric', 'min:0.01', 'max:100'];
        }

        if ($this->input('tipe') === 'kelebihan_bayar') {
            $rules['nominal_seharusnya'] = ['required', 'integer', 'min:1000'];
            $rules['nominal_dibayar'] = ['required', 'integer', 'min:1000', 'gt:nominal_seharusnya'];
        }

        // Untuk tipe biasa & digital, nominal harus diisi
        if (in_array($this->input('tipe'), ['biasa', 'digital'])) {
            $rules['nominal'] = ['required', 'integer', 'min:1000'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'tipe.required' => 'Tipe nota harus dipilih',
            'tanggal_nota.required' => 'Tanggal nota wajib diisi',
            'tanggal_nota.before_or_equal' => 'Tanggal nota tidak boleh lebih dari hari ini',
            'divisi_id.required' => 'Divisi/Lokasi harus dipilih',
            'nomor_nota.unique' => 'Nomor nota sudah terdaftar di sistem',
            'keterangan.required' => 'Keterangan tidak boleh kosong',
            'keterangan.min' => 'Keterangan minimal 5 karakter',
            'attachments.required' => 'Foto lampiran tidak boleh kosong (minimal 1 foto)',
            'attachments.min' => 'Minimal ada 1 foto lampiran',
            'attachments.*.max' => 'Ukuran file maksimal 5MB',
            'attachments.*.mimes' => 'Format file harus JPG, PNG, atau HEIC',
            'split_items.required' => 'Minimal 2 divisi untuk split tagihan',
            'split_items.min' => 'Minimal 2 divisi untuk split tagihan',
            'split_items.max' => 'Maksimal 20 divisi untuk split tagihan',
            'split_items.*.divisi_id.required' => 'Divisi harus dipilih untuk setiap item',
            'split_items.*.divisi_id.integer' => 'ID divisi tidak valid',
            'split_items.*.divisi_id.exists' => 'Divisi yang dipilih tidak ditemukan',
            'split_items.*.nominal.required' => 'Nominal harus diisi untuk setiap divisi',
            'split_items.*.nominal.integer' => 'Nominal harus berupa angka',
            'split_items.*.nominal.min' => 'Nominal minimal Rp 1.000 untuk setiap divisi',
            'nominal_total.required' => 'Nominal total harus diisi untuk split tagihan',
            'nominal_total.integer' => 'Nominal total harus berupa angka',
            'nominal_total.min' => 'Nominal total minimal Rp 2.000',
            'base_amount.required' => 'Base amount harus diisi',
            'persentase.required' => 'Persentase harus diisi',
            'persentase.max' => 'Persentase maksimal 100%',
            'nominal_seharusnya.required' => 'Nominal seharusnya dibayar harus diisi',
            'nominal_dibayar.gt' => 'Nominal dibayar harus lebih dari nominal seharusnya',
        ];
    }

    /**
     * Get bulan & tahun dari tanggal_nota
     * Cast nominal dan split_items ke integer
     */
    protected function prepareForValidation(): void
    {
        $date = \Carbon\Carbon::parse($this->tanggal_nota);

        $data = [
            'bulan' => $date->month,
            'tahun' => $date->year,
        ];

        // Cast nominal fields ke integer
        if ($this->has('nominal')) {
            $data['nominal'] = (int) $this->input('nominal', 0);
        }
        if ($this->has('nominal_total')) {
            $data['nominal_total'] = (int) $this->input('nominal_total', 0);
        }
        if ($this->has('base_amount')) {
            $data['base_amount'] = (int) $this->input('base_amount', 0);
        }
        if ($this->has('nominal_seharusnya')) {
            $data['nominal_seharusnya'] = (int) $this->input('nominal_seharusnya', 0);
        }
        if ($this->has('nominal_dibayar')) {
            $data['nominal_dibayar'] = (int) $this->input('nominal_dibayar', 0);
        }

        // Cast split_items nominal ke integer
        if ($this->has('split_items') && is_array($this->input('split_items'))) {
            $splitItems = $this->input('split_items');
            foreach ($splitItems as $key => $item) {
                if (isset($item['nominal'])) {
                    $splitItems[$key]['nominal'] = (int) $item['nominal'];
                }
            }
            $data['split_items'] = $splitItems;
        }

        $this->merge($data);
    }
}
