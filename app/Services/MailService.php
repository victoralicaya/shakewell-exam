<?php

namespace App\Services;

use App\Mail\RegisterMail;
use Illuminate\Support\Facades\Mail;

class MailService
{
    public function sendRegistrationMail($user, $voucher)
    {
        Mail::to($user->email)->send(new RegisterMail($user, $voucher));
    }
}
