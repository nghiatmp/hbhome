<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        Log::info($credentials);
        if ($token = $this->guard()->attempt($credentials)) {
            return response()->json(['message' => 'success', 'user' => $this->guard()->user()], Response::HTTP_OK)
                ->header('Authorization', $token);
        }
        return response()->json([], Response::HTTP_UNAUTHORIZED);
    }

    public function logout()
    {
        $this->guard()->logout();
        return response()->json(['message' => 'success'], Response::HTTP_OK);
    }

    public function refresh()
    {
        try {
            if ($token = $this->guard()->getToken()) {
                $newToken = $this->guard()->refresh($token);
                $this->guard()->setToken($newToken);
                return response()
                    ->json(['message' => 'success'], Response::HTTP_OK)
                    ->header('Authorization', $newToken);
            }
            return response()->json(['error' => 'refresh_token_error'], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $exception) {
            return response()->json(['error' => 'refresh_token_error'], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function user(Request $request)
    {
        $user = $this->guard()->user();
        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    /**
     * @return mixed
     */
    private function guard()
    {
        return Auth::guard('api');
    }
}
