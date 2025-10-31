<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KhotibJumatRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nama_khotib' => 'required|string|max:100',
            'nama_imam' => 'required|string|max:100',
            'tema_khutbah' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'foto_khotib' => 'nullable|image|max:2048'
        ];
    }
}
