<?php

namespace App\Http\Controllers\API\Users;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use Illuminate\Http\Request;
use BenSampo\Enum\Rules\EnumValue;
use App\Enums\SystemRole;
use App\Services\UserService;

/**
 * @group Users
 */
class StoreController extends Controller
{
    protected $userService;
    protected $teamService;

    public function __construct(UserService $userService, TeamService $teamService)
    {
        $this->userService = $userService;
        $this->teamService = $teamService;
    }

    /**
     * Store
     * Add new user with given input
     *
     * @bodyParam full_name string required
     * @bodyParam email string required
     * @bodyParam role integer required
     *
     * @return Object
     *
     * @response {
     *   "id": "1",
     *   "full_name": "Admin",
     *   "email": "admin@hblab.co.jp",
     *   "role": "7",
     *   "created_at": "2019-02-22 01:52:39",
     *   "updated_at": "2019-02-22 01:52:39"
     * }
     *
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {}
     * }
     */
    public function main(Request $request)
    {
        $this->validation($request);
        $params = $this->getData($request);
        $teamID = $params['team_id'] ?? 0;
        $teamID = intval($teamID);
        if ($teamID != 0) {
            //Check parent team is exits
            $team = $this->teamService->find($teamID);
            if (is_null($team)) {
                return response()->json(
                    ["message" => 'Parent team is not valid'],
                    404
                );
            }
        }
        $params['password'] = bcrypt($request->get('email'));
        $responseData = $this->userService->store($params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'full_name' => 'required|string|max:127',
            'email' => 'required|email|max:127|unique:users,email',
            'role' => ['required', new EnumValue(SystemRole::class, false)],
        ]);
    }

    protected function getData(Request $request)
    {
        return $request->only(['full_name', 'email', 'role', 'team_id']);
    }
}
