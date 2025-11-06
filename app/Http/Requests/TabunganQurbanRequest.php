<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TabunganQurbanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Pastikan hanya admin yang bisa, sesuaikan dengan middleware Anda
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Sesuaikan 'nama_hewan' dengan ENUM di database Anda
        $namaHewanEnum = ['kambing', 'domba', 'sapi', 'kerbau', 'unta'];

        return [
            'id_pengguna' => 'required|string|exists:pengguna,id_pengguna',
            'nama_hewan' => ['required', 'string', Rule::in($namaHewanEnum)],
            'total_hewan' => 'required|integer|min:1',
            'total_harga_hewan_qurban' => 'required|numeric|min:0',
        ];
    }
}
