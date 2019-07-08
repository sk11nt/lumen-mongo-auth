<?php
declare (strict_types=1);

namespace App\Services;

use App\Contracts\QueueMessage as QueueMessageInterface;
use App\Contracts\QueueService;
use App\QueueMessage;

class MongoQueue implements QueueService
{
    public function push(string $queue, array $message): void
    {
        QueueMessage::create([
            'queue' => $queue,
            'message' => $message,
            'attempts' => 0,
            'lock' => false,
        ]);
    }

    public function pop(string $queue): ?QueueMessageInterface
    {
        return QueueMessage::where('queue', '=', $queue)
            ->where('lock', '=', false)
            ->orderBy('updated_at', 'asc')
            ->first();
    }
}
