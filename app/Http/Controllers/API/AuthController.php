<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AuthController extends ApiController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return string
     */
    public function login(Request $request)
    {
        $this->validator($request, [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6'
        ]);

        $credentials = ['email' => $request->get('email'), 'password' => $request->get('password')];

        if (!$token = Auth::attempt($credentials)) {
            return $this->unauthorized('Unauthorized');
        }

        return $this->success($this->respondWithToken($token));
    }

    /**
     * Get the authenticated User.
     *
     * @return string
     */
    public function me()
    {
        return $this->success(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return $this->success(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return string
     */
    public function refresh()
    {
        return $this->success($this->respondWithToken(auth()->refresh()));
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return array
     */
    protected function respondWithToken($token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }
}
