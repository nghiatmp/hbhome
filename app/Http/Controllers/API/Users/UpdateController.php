<?php

namespace App\Http\Controllers\API\Users;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use BenSampo\Enum\Rules\EnumValue;
use App\Enums\SystemRole;
use App\Services\UserService;

/**
 * @group Users
 */
class UpdateController extends Controller
{
    protected $userService;
    protected $teamService;

    public function __construct(UserService $userService, TeamService $teamService)
    {
        $this->userService = $userService;
        $this->teamService = $teamService;
    }

    /**
     * Update
     * Update user with given input
     *
     * @bodyParam userId integer required
     * @bodyParam full_name string required
     * @bodyParam email string required
     * @bodyParam role integer required
     *
     * @return Object
     *
     * @response 200 {
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
     *
     * @response 404 {
     *   "message": "Not Found",
     *   "errors": {}
     * }
     */
    public function main(Request $request)
    {
        $this->validation($request);
        $params = $this->getData($request);
        $responseData = $this->userService->update($request->userId, $params);
        $responseData = $responseData->toArray();
        if (isset($params['team_id'])) {
            $responseData['available_team'][0] = $this->teamService->find($params['team_id'])->toArray();
        } else {
            $responseData['available_team'] = [];
        }

        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'full_name' => 'required|string|max:127',
            'email' => [
                'required',
                'email',
                'between:1,127',
                Rule::unique('users')->ignore($request->userId),
            ],
            'role' => ['required', new EnumValue(SystemRole::class, false)],
        ]);
    }

    protected function getData(Request $request)
    {
        return $request->only(['full_name', 'role', 'email', 'team_id']);
    }
}
