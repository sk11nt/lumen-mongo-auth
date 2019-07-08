<?php
declare (strict_types=1);

namespace App\Http\Controllers;

use App\Services\Registration;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @param Registration $userService
     * @return JsonResponse
     * @throws ValidationException
     */
    public function register(Request $request, Registration $userService): JsonResponse
    {
        $data = $this->validate($request, [
            'username' => 'required|unique:users',
            'password' => 'required',
            'email' => 'bail|required|unique:users|email',
        ]);

        $user = $userService->registerUser(
            $data['username'],
            $data['email'],
            $data['password']
        );

        return response()->json($user, SymfonyResponse::HTTP_CREATED);
    }

    /**
     * @param string $code
     * @param Registration $userService
     * @return Response
     */
    public function activate(string $code, Registration $userService): Response
    {
        $userService->activate($code);

        return response(null, SymfonyResponse::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @param Registration $userService
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function authenticate(Request $request, Registration $userService): JsonResponse
    {
        $data = $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);

        $token = $userService->authenticate($data['username'], $data['password']);

        return response()->json($token, SymfonyResponse::HTTP_CREATED);
    }
}
