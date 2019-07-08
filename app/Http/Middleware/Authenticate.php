<?php

namespace App\Http\Middleware;

use App\Services\Registration;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\ResponseFactory;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Authenticate
{
    /**
     * @var Registration
     */
    protected $registration;

    /**
     * @param Registration $registration
     */
    public function __construct(Registration $registration)
    {
        $this->registration = $registration;
    }

    /**
     * Handles incoming request and runs token validation.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response|ResponseFactory|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->registration->validateToken($request->bearerToken())) {
            return response('Unauthorized.', SymfonyResponse::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
