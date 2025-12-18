<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // 1. Tambahkan baris ini
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// 2. Tambahkan 'implements ShouldQueue' di sini
class OtpVerificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->subject('Kode Verifikasi OTP Smart Masjid')
            ->view('emails.otp');
    }
}