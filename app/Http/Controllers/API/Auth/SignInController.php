<?php

namespace App\Http\Controllers\API\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AuthService;

/**
 * @group Auth
 */
class SignInController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Sign-In
     * Sign In with given Credentials
     *
     * @return Object
     *
     * @bodyParam email string required Email to login
     * @bodyParam password string required Password to login
     *
     * @response {
     *   "user": {
     *      "id": "1",
     *      "full_name": "Admin",
     *      "email": "admin@hblab.co.jp"
     *   },
     *   "access_token": "xxxx"
     * }
     *
     * @response 400 {}
     * @response 422 {}
     */
    public function main(Request $request)
    {
        $this->validation($request);
        $credentials = $this->getData($request);
        $responseData = $this->authService->signIn($credentials);
        return response()->json($responseData, 200);
    }

    protected function getData(Request $request)
    {
        return $request->only(['email', 'password']);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'email' => 'required|string|email|max:127',
            'password' => 'required|string|min:6|max:32',
        ]);
    }
}
