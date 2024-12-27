<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendOtpCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->subject('Kode OTP Verifikasi')
                    ->view('emails.otp')
                    ->with(['otp' => $this->otp]);
    }
}
