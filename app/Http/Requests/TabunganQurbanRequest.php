<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TabunganQurbanRequest extends FormRequest
{
    public function authorize(): bool
    {

        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {

        $namaHewanEnum = ['kambing', 'domba', 'sapi', 'kerbau', 'unta'];

        return [
            'id_pengguna' => 'required|string|exists:pengguna,id_pengguna',
            'nama_hewan' => ['required', 'string', Rule::in($namaHewanEnum)],
            'total_hewan' => 'required|integer|min:1',
            'total_harga_hewan_qurban' => 'required|numeric|min:0',
        ];
    }
}
