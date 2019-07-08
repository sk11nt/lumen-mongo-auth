<?php
declare (strict_types=1);

namespace App\Services;

use App\Contracts\PasswordEncoder;
use App\Events\UserCreatedEvent;
use App\Token;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use MongoDB\BSON\UTCDateTime;

class Registration
{
    /** @var PasswordEncoder */
    private $encoder;

    /**
     * Registration constructor.
     * @param PasswordEncoder $encoder
     */
    public function __construct(PasswordEncoder $encoder)
    {
        $this->encoder = $encoder;
    }

    public function registerUser(string $username, string $email, string $password): User
    {
        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->password = $this->encoder->encode($password);
        $user->activation_code = static::generateActivationCode();;
        $user->save();

        event(new UserCreatedEvent($user));

        return $user;
    }

    public function activate(string $activationCode): bool
    {
        return (bool) User::where('activation_code', '=', $activationCode)->update(['activation_code' => null]);
    }

    public function authenticate(string $username, string $password): Token
    {
        /** @var User $user */
        $user = User::where('username', '=', $username)->first();

        if (is_null($user) || !$this->encoder->isEqual($password, $user->password)) {
            throw new AuthorizationException(sprintf("User with username '%s' and password '%s' was not found.", $username, $password));
        }

        if (!is_null($user->activation_code)) {
            throw new AuthorizationException("User is inactive.");
        }

        $token = Token::create([
            'token' => static::generateToken(),
            'expires_at' => new UTCDateTime((time() + 60*60*24*30)*1000),
            'user_id' => $user->_id,
        ]);

        $token->user_id = $user->_id;
        $token->save();

        return $token;
    }

    public function validateToken(string $token): bool
    {
        return Token::where('token', '=', $token)
            ->where('expires_at', '>', new UTCDateTime())
            ->exists();
    }

    protected static function generateActivationCode(): string
    {
        return md5(uniqid());
    }

    protected static function generateToken(): string
    {
        return md5(uniqid());
    }
}
