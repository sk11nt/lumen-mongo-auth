<?php
declare (strict_types=1);

namespace App\Events;

use App\User;

class UserCreatedEvent
{
    /** @var User */
    private $user;

    /**
     * UserCreatedEvent constructor.
     * @param $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->user->_id;
    }

    /**
     * @return string
     */
    public function getUserEmail(): string
    {
        return $this->user->email;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->user->username;
    }

    /**
     * @return string
     */
    public function getActivationCode(): string
    {
        return $this->user->activation_code;
    }
}
