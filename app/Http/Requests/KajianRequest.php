<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KajianRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nama_penceramah' => 'required|string|max:100',
            'tema_kajian'     => 'required|string|max:255',
            
            // --- BAGIAN INI WAJIB ADA! ---
            // Tanpa baris ini, data 'jenis_kajian' akan dibuang Laravel
            // Akibatnya database error "null value"
            'jenis_kajian'    => 'required|in:event,harian', 
            // -----------------------------

            'tanggal_kajian'  => 'required|date',
            'waktu_kajian'    => 'nullable',
            'foto_penceramah' => 'nullable|image|max:2048'
        ];
    }
}