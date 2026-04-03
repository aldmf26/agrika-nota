<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi untuk APPROVE nota
 */
class ApproveNotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $nota = $this->route('nota');
        return $this->user()->can('approve', $nota);
    }

    public function rules(): array
    {
        return [
            'catatan_approver' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'catatan_approver.max' => 'Catatan maksimal 500 karakter',
        ];
    }
}
