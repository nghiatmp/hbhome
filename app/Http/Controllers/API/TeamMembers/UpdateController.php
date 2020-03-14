<?php

namespace App\Http\Controllers\API\TeamMembers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use BenSampo\Enum\Rules\EnumValue;
use App\Enums\TeamRole;
use App\Services\UserTeamService;

/**
 * @group Team Members
 */
class UpdateController extends Controller
{
    protected $userTeamService;

    public function __construct(UserTeamService $userTeamService)
    {
        $this->userTeamService = $userTeamService;
    }
    
    /**
     * Update
     * Update team member with given input
     *
     * @bodyParam teamMemberId integer required
     * @bodyParam role integer required
     *
     * @return Object
     *
     * @response {
     *   "id": 1,
     *   "team_id": 1,
     *   "user_id": 1,
     *   "role": "3",
     *   "created_at": "2019-02-22 01:52:39",
     *   "updated_at": "2019-02-22 01:52:39"
     * }
     *
     * @response 403 {
     *   "message": "Forbidden",
     *   "errors": {}
     * }
     *
     * @response 404 {
     *   "message": "Not Found",
     *   "errors": {}
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
        $responseData = $this->userTeamService->update($request->teamMemberId, $params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'role' => ['required', new EnumValue(TeamRole::class, false)],
        ]);
    }

    protected function getData(Request $request)
    {
        return $request->only(['role']);
    }
}
