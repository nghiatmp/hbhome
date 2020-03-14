<?php

namespace App\Http\Controllers\API\Teams;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TeamService;

/**
 * @group Teams
 */
class StoreController extends Controller
{
    protected $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }
    
    /**
     * Store
     * Add new team with given input
     *
     * @bodyParam title string required
     *
     * @return Object
     *
     * @response {
     *   "id": "1",
     *   "title": "Abc",
     *   "parent_id": 0,
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
        $parentID = $params['parent_id'];
        if ($parentID != 0) {
            //Check parent team is exits
            $parentTeam = $this->teamService->find($parentID);
            if (is_null($parentTeam)) {
                return response()->json(
                    ["message" => 'Parent team is not valid'],
                    404
                );
            }
        }
        $responseData = $this->teamService->store($params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'title' => 'required|string|max:127|unique:teams,title',
            'parent_id' => 'required|integer',
        ]);
    }

    protected function getData(Request $request)
    {
        return $request->only(['title', 'parent_id']);
    }
}
