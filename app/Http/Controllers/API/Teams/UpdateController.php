<?php

namespace App\Http\Controllers\API\Teams;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TeamService;

/**
 * @group Teams
 */
class UpdateController extends Controller
{
    protected $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    /**
     * Update
     * Update team with given input
     *
     * @bodyParam teamId integer required
     * @bodyParam title string required
     *
     * @return Object
     *
     * @response {
     *   "id": 29,
     *   "title": "abc",
     *   "updated_at": "2019-02-25 09:26:57",
     *   "created_at": "2019-02-26 09:26:57"
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
//        $parentID = $params['parent_id'];
//        if ($parentID != 0) {
//            //Check parent team is exits
//            $parentTeam = $this->teamService->find($parentID);
//            if (is_null($parentTeam)) {
//                return response()->json(
//                    ["message" => 'Parent team is not valid'],
//                    404
//                );
//            }
//        }
        $responseData = $this->teamService->update($request->teamId, $params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'title' => 'required|string|between:1,127',
//            'parent_id' => 'required|integer',
        ]);
    }

    protected function getData(Request $request)
    {
        return $request->only(['title', 'parent_id']);
    }
}
