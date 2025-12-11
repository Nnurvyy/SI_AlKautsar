<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PemasukanQurbanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'id_tabungan_hewan_qurban' => 'required|string|exists:tabungan_hewan_qurban,id_tabungan_hewan_qurban',
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:1',
        ];
    }
}
