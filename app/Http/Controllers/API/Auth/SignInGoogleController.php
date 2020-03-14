<?php

namespace App\Http\Controllers\API\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AuthService;

/**
 * @group Auth
 */
class SignInGoogleController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Sign-In
     *
     * @return Object
     *
     * @bodyParam access_token string required
     *
     * @response {
     *   "user": {
     *      "id": "1",
     *      "full_name": "Admin",
     *      "role": 7
     *   },
     *   "access_token": "xxxx"
     * }
     *
     * @response 401 {}
     * @response 422 {}
     */
    public function main(Request $request)
    {
//        $this->validation($request);
        $data = $this->getData($request);
        $responseData = $this->authService->signInGoogle($data);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'access_token' => 'required|string',
        ]);
    }

    protected function getData(Request $request)
    {
        return $request->only(['access_token']);
    }
}
