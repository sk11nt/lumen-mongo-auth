<?php

namespace App\Contracts;

interface QueueMessage
{
    public function getQueue(): string;

    public function getMessage(): array;

    public function lock(): void;

    public function unlock(): void;

    public function succeeded(): void;

    public function failed(): void;
}
