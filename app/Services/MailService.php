<?php
declare (strict_types=1);

namespace App\Services;

use App\Contracts\MailService as MailServiceInterface;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

class MailService implements MailServiceInterface
{
    public function sendActivationCode(string $email, string $name, string $code): bool
    {
        Mail::raw(sprintf("Dear %s. Your activation code is \"%s\"", $name, $code), function(Message $message) use ($email) {
            $message->to($email);
        });
    }
}
