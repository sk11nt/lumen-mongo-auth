<?php
declare (strict_types=1);

namespace App\Contracts;

interface PasswordEncoder
{
    /**
     * @param string $string
     * @return string
     */
    public function encode(string $string): string;

    /**
     * @param string $value
     * @param string $hashedValue
     * @return bool
     */
    public function isEqual(string $value, string $hashedValue): bool;
}
