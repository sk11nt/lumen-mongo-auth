<?php
declare (strict_types=1);


namespace App\Services;


use App\Contracts\PasswordEncoder;
use Illuminate\Support\Facades\Hash;

class PasswordHashEncoder implements PasswordEncoder
{
    /**
     * {@inheritDoc}
     */
    public function encode(string $string): string
    {
        return Hash::make($string);
    }

    /**
     * {@inheritDoc}
     */
    public function isEqual(string $value, string $hashedValue): bool
    {
        return Hash::check($value, $hashedValue);
    }
}
