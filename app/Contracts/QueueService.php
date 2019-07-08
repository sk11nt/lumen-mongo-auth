<?php

namespace App\Contracts;

interface QueueService
{
    /**
     * @param string $queue
     * @param array $message
     */
    public function push(string $queue, array $message): void;

    /**
     * @param string $queue
     * @return QueueMessage|null
     */
    public function pop(string $queue): ?QueueMessage;
}
