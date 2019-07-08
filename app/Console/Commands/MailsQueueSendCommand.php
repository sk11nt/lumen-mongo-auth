<?php
declare (strict_types=1);

namespace App\Console\Commands;

use App\Contracts\MailService;
use App\Contracts\QueueService;
use Illuminate\Console\Command;

class MailsQueueSendCommand extends Command
{
    protected $signature = "mails-queue:send";
    protected $description = "Retrieves all emails from queue and sends them out";

    /** @var QueueService */
    private $queueService;
    /** @var MailService */
    private $mailService;

    /**
     * MailsQueueSendCommand constructor.
     * @param QueueService $queueService
     * @param MailService $mailService
     */
    public function __construct(QueueService $queueService, MailService $mailService)
    {
        parent::__construct();

        $this->queueService = $queueService;
        $this->mailService = $mailService;
    }

    public function handle()
    {
        try {
            while ($message = $this->queueService->pop('mails')) {
                $message->lock();
                $msgData = $message->getMessage();

                if ($this->mailService->sendActivationCode($msgData['email'], $msgData['username'], $msgData['activation_code'])) {
                    $message->succeeded();

                    $this->info(sprintf("Email to %s was sent", $msgData['email']));
                } else {
                    $message->failed();

                    $this->info(sprintf("Email to %s was not sent, requeueing.", $msgData['email']));
                }
            }
        } catch (\Exception $e) {
            $this->error(sprintf("An error occurred, %s", $e->getMessage()));
        }
    }
}
