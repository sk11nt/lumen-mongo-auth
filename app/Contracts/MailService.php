<?php

namespace App\Contracts;

interface MailService
{
    /**
     * @param string $email
     * @param string $name
     * @param string $code
     * @return bool
     */
    public function sendActivationCode(string $email, string $name, string $code): bool;
}
