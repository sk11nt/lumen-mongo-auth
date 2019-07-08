<?php
declare (strict_types=1);

namespace App\Listeners;

use App\Contracts\QueueService;
use App\Events\UserCreatedEvent;

class NewUserSendEmail
{
    /** @var QueueService */
    private $queueService;

    public function __construct(QueueService $queueService)
    {
        $this->queueService = $queueService;
    }

    /**
     * @param UserCreatedEvent $event
     */
    public function handle(UserCreatedEvent $event)
    {
        $this->queueService->push('mails', [
            'user_id' => $event->getUserId(),
            'email' => $event->getUserEmail(),
            'username' => $event->getUserName(),
            'activation_code' => $event->getActivationCode(),
        ]);
    }
}
