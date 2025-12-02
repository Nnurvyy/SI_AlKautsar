<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KajianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ubah jadi true agar bisa diakses
    }

    public function rules(): array
    {
        return [
            'tipe' => 'required|in:rutin,event',
            'nama_penceramah' => 'required|string|max:100',
            'tema_kajian' => 'required|string|max:255',
            
            // Jika tipe = event, tanggal wajib. Jika rutin, tanggal boleh kosong.
            'tanggal_kajian' => 'required_if:tipe,event|nullable|date',
            
            // Jika tipe = rutin, hari wajib.
            'hari' => 'required_if:tipe,rutin|nullable|string',
            
            'waktu_kajian' => 'nullable',
            'foto_penceramah' => 'nullable|image|max:2048',
        ];
    }
}