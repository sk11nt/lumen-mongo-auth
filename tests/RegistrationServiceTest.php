<?php
declare (strict_types=1);

use App\Events\UserCreatedEvent;
use App\Services\Registration;
use App\Token;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;

class RegistrationServiceTest extends TestCase
{
    private const USERNAME = 'username';
    private const EMAIL = 'email@domain.com';
    private const PASSWORD = 'password';

    /** @var Registration */
    protected $registrationService;

    public function setUp(): void
    {
        parent::setUp();
        $this->registrationService = $this->app->make(Registration::class);
    }

    public function testRegisterUser()
    {
        $this->expectsEvents(UserCreatedEvent::class);

        $this->registrationService->registerUser(self::USERNAME, self::EMAIL, self::PASSWORD);
        $this->seeInDatabase('users', ['email' => self::EMAIL]);

        User::where('email', '=', self::EMAIL)->delete();
    }

    public function testActivate()
    {
        $this->registrationService->registerUser(self::USERNAME, self::EMAIL, self::PASSWORD);
        $user = User::where('email', '=', self::EMAIL)->first();
        $this->assertNotNull($user->activation_code);

        $this->registrationService->activate($user->activation_code);
        $user->refresh();
        $this->assertNull($user->activation_code);

        User::where('email', '=', self::EMAIL)->delete();
    }

    public function testAuthenticate()
    {
        $this->registrationService->registerUser(self::USERNAME, self::EMAIL, self::PASSWORD);
        $user = User::where('email', '=', self::EMAIL)->first();
        $this->registrationService->activate($user->activation_code);

        $token = $this->registrationService->authenticate(self::USERNAME, self::PASSWORD);

        $this->assertInstanceOf(Token::class, $token);
        $this->assertEquals($token->user->email, self::EMAIL);

        User::where('email', '=', self::EMAIL)->delete();
    }

    public function testAuthenticateException1()
    {
        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage(sprintf("User with username '%s' and password '%s' was not found.", self::USERNAME . 'a', self::PASSWORD . 'a'));

        $this->registrationService->authenticate(self::USERNAME . 'a', self::PASSWORD . 'a');
    }

    public function testAuthenticateException2()
    {
        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage("User is inactive.");

        $this->registrationService->registerUser(self::USERNAME, self::EMAIL, self::PASSWORD);
        User::where('email', '=', self::EMAIL)->first();

        $this->registrationService->authenticate(self::USERNAME, self::PASSWORD);

        User::where('email', '=', self::EMAIL)->delete();
    }

    public function testValidateToken()
    {
        $this->registrationService->registerUser(self::USERNAME, self::EMAIL, self::PASSWORD);
        $token = Token::first();

        $this->assertTrue($this->registrationService->validateToken($token->token));
        $this->assertFalse($this->registrationService->validateToken(uniqid()));

        Token::where('token', '=', $token->token)->delete();
        User::where('email', '=', self::EMAIL)->delete();
    }

}
