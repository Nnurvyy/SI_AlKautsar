<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KajianRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Izinkan admin
    }

    public function rules()
    {
        // Sesuaikan dengan Model Kajian
        return [
            'nama_penceramah' => 'required|string|max:100',
            'tema_kajian' => 'required|string|max:255',
            'tanggal_kajian' => 'required|date',
            'waktu_kajian' => 'nullable|date_format:H:i',
            'foto_penceramah' => 'nullable|image|max:2048'
        ];
    }
}