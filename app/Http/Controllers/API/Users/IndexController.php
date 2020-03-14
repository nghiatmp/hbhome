<?php

namespace App\Http\Controllers\API\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService;

/**
 * @group Users
 */
class IndexController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    
    /**
     * Index
     * Get list users
     *
     * @queryParam page integer nullable
     *
     * @return Array Objects
     *
     * @response 200 {
     *  "data": [
     *      {
     *      "id": 1,
     *      "full_name": "Admin",
     *      "email": "admin@hblab.co.jp",
     *      "role": "7"
     *      }
     *  ],
     *  "current_page": 1,
     *  "per_page": 20,
     *  "last_page": 1,
     *  "total": 12
     * }
     */
    public function main(Request $request)
    {
        $responseData = $this->userService->index();
        return response()->json($responseData, 200);
    }
}
