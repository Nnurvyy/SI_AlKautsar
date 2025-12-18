<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // <--- WAJIB ADA
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PeringatanTunggakanMail extends Mailable implements ShouldQueue // <--- WAJIB IMPLEMENTS
{
    use Queueable, SerializesModels;

    public $data;

    // Kita terima semua data yang dibutuhkan lewat constructor
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('Peringatan Tunggakan Tabungan Qurban')
                    ->view('emails.peringatan_tunggakan'); // Kita akan buat view ini
    }
}