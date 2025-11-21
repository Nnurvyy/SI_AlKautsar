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
            // TAMBAHKAN INI AGAR TIPE TIDAK NULL
            'tipe' => 'required|in:rutin,event', 
            
            'nama_penceramah' => 'required|string|max:100',
            'tema_kajian' => 'required|string|max:255',
            'tanggal_kajian' => 'required|date',
            'waktu_kajian' => 'nullable', // sesuaikan format time jika perlu
            'foto_penceramah' => 'nullable|image|max:2048',
        ];
    }
}