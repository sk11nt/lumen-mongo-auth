<?php
declare (strict_types=1);

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
use App\Contracts\QueueMessage as QueueMessageInterface;
use MongoDB\BSON\UTCDateTime;

class QueueMessage extends Model implements QueueMessageInterface
{
    protected $connection = 'mongodb';

    protected const MAX_ATTEMPTS = 5;

    protected $fillable = [
        'queue', 'message', 'attempts', 'lock',
    ];

    protected $dates = [
        'locked_at'
    ];

    public function getQueue(): string
    {
        return $this->queue;
    }

    public function getMessage(): array
    {
        return $this->message;
    }

    public function lock(): void
    {
        $this->locked = true;
        $this->locked_at = new UTCDateTime();
        $this->save();
    }

    public function unlock(): void
    {
        $this->locked = true;
        $this->locked_at = null;
        $this->save();
    }

    public function succeeded(): void
    {
        $this->delete();
    }

    public function failed(): void
    {
        if ($this->attempts >= static::MAX_ATTEMPTS) {
            $this->delete();
            //TODO: Add log record.
        } else {
            $this->attempts = $this->attempts + 1;
            $this->unlock();
        }
    }
}
