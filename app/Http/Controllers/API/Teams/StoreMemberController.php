<?php

namespace App\Http\Controllers\API\Teams;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserTeamService;

/**
 * @group Teams
 */
class StoreMemberController extends Controller
{
    protected $userTeamService;

    public function __construct(UserTeamService $userTeamService)
    {
        $this->userTeamService = $userTeamService;
    }
    
    /**
     * Store member
     * Add new member to team by id
     *
     * @bodyParam teamId integer required
     * @bodyParam userIds array required
     *
     * @return Object
     *
     * @response {
     *   "team_id": 29,
     *   "user_id": 1,
     *   "role": "3",
     *   "created_at": "2019-02-25 09:26:57",
     *   "updated_at": "2019-02-25 09:26:57"
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
        $userIDs = isset($params['userIds']) ? $params['userIds'] : [];
        $userTeamValidate = $this->userTeamService->validateDataStore($userIDs);
        if ($userTeamValidate > 0) {
            return response()->json(['message' => "User(s) belong to another team"], 422);
        }
        $responseData = $this->userTeamService->store($request->teamId, $params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'userIds' => 'required|array',
        ]);
    }

    protected function getData(Request $request)
    {
        return $request->only('userIds');
    }
}
