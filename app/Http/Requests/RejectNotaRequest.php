<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi untuk REJECT nota
 */
class RejectNotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $nota = $this->route('nota');
        return $this->user()->can('reject', $nota);
    }

    public function rules(): array
    {
        return [
            'catatan_approver' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'catatan_approver.required' => 'Alasan penolakan harus diisi',
            'catatan_approver.min' => 'Alasan minimal 10 karakter',
            'catatan_approver.max' => 'Alasan maksimal 500 karakter',
        ];
    }
}
