<?php

namespace App\Http\Controllers\API\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\UserService;

/**
 * @group Users
 */
class SuggestController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    
    /**
     * Suggest
     * Suggest users
     *
     * @queryParam keyword required string
     *
     * @return Array
     *
     * @response 200 {
     *  "data": [
     *      {
     *          "id": 1,
     *          "full_name": "Admin",
     *          "email": "admin@hblab.co.jp"
     *      },
     *      {
     *          "id": 2,
     *          "full_name": "Admin",
     *          "email": "admin@hblab.co.jp"
     *      }
     *  ]
     * }
     */
    public function main(Request $request)
    {
        $this->validation($request);
        $params = $this->getData($request);
        $responseData = $this->userService->suggest($params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'keyword' => 'required|string|between:1,127',
        ]);
    }

    protected function getData(Request $request)
    {
        return $request->only(['keyword']);
    }
}
