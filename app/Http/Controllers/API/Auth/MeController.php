<?php

namespace App\Http\Controllers\API\Auth;

use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * @group Auth
 */
class MeController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Me
     * Get My Info from Token
     *
     * @return Object
     *
     * @response {
     *   "id": 1,
     *   "full_name": "Admin",
     *   "role": 15
     * }
     */
    public function main(Request $request)
    {
        return response()->json($this->authService->getMe(), 200);
    }
}
