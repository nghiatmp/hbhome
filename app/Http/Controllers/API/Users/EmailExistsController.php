<?php

namespace App\Http\Controllers\API\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService;

/**
 * @group Users
 */
class EmailExistsController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    
    /**
     * Email exists
     * Check email exists
     *
     * @bodyParam email string required
     *
     * @return Object
     *
     * @response 200 {
     *  "has_email": true
     * }
     */
    public function main(Request $request)
    {
        $this->validation($request);
        $params = $this->getData($request);
        $responseData = $this->userService->emailExists($params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'email' => [
                'required',
                'email',
            ],
        ]);
    }

    protected function getData(Request $request)
    {
        return $request->only(['email']);
    }
}
