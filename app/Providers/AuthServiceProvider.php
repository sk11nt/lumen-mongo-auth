<?php

namespace App\Providers;

use App\Token;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use MongoDB\BSON\UTCDateTime;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['auth']->viaRequest('api', function (Request $request) {
            if ($token = $request->bearerToken()) {
                return Token::where('token', '=', $token)
                    ->where('expires_at', '>', new UTCDateTime())
                    ->first()
                    ->user;
            }
        });
    }
}
