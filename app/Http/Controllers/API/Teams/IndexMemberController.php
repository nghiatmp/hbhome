<?php

namespace App\Http\Controllers\API\Teams;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use Illuminate\Http\Request;
use App\Services\UserTeamService;

/**
 * @group Teams
 */
class IndexMemberController extends Controller
{
    protected $userTeamService;
    protected $teamService;

    public function __construct(
        UserTeamService $userTeamService,
        TeamService $teamService
    ) {
        $this->userTeamService = $userTeamService;
        $this->teamService = $teamService;
    }

    /**
     * Index member
     * Get all members in a team
     *
     * @queryParam from date nullable
     * @queryParam to date nullable
     *
     * @return Array
     *
     * @response {
     *  "from": "2019-04-01",
     *  "to": "2019-04-30",
     *  "members": [
     *      {
     *          "id": 1,
     *          "user_id": 1,
     *          "role": "1",
     *          "busy_rate": 87,
     *          "user": {
     *              "id": 1,
     *              "full_name": "ada",
     *              "email": "abc@aojda"
     *          }
     *      },
     *      {
     *          "id": 2,
     *          "user_id": 2,
     *          "role": "1",
     *          "busy_rate": 87.2,
     *          "user": {
     *              "id": 2,
     *              "full_name": "adad",
     *              "email": "adada@ahka"
     *          }
     *      }
     *  ]
     * }
     */
    public function main(Request $request)
    {
        $this->validation($request);
        $params = $this->getParams($request);
        //get all teamId children
        $teamIds = $this->teamService->getAllChildrenTeam($request->teamId);
        $responseData = $this->userTeamService->findByParentTeamIds($teamIds, $params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);
    }

    protected function getParams(Request $request)
    {
        $params['from'] = null;
        $params['to'] = null;
        if ($request->has(['from', 'to'])) {
            $params['from'] = $request->from;
            $params['to'] = $request->to;
        }
        return $params;
    }
}
