<?php

namespace App\Http\Controllers\API\Teams;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use Illuminate\Http\Request;
use App\Services\EffortService;

/**
 * @group Teams
 */
class EffortController extends Controller
{
    protected $effortService;
    protected $teamService;

    public function __construct(EffortService $effortService, TeamService $teamService)
    {
        $this->effortService = $effortService;
        $this->teamService = $teamService;
    }

    /**
     * Effort
     * Get team's effort per month
     *
     * @bodyParam teamId integer required
     * @queryParam from date nullable
     * @queryParam to date nullable
     *
     * @return Array
     *
     * @response {
     *  "from": "2019-01-01",
     *  "to": "2019-03-11",
     *  "efforts": [
     *      {
     *          "month": "2019-01",
     *          "mm": 0.17
     *      },
     *      {
     *          "month": "2019-02",
     *          "mm": 0.43
     *      }
     *  ]
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
     */
    public function main(Request $request)
    {
        $this->validation($request);
        $params = $this->getParams($request);
        //get all teamId children
        $teamIds = $this->teamService->getAllChildrenTeam($request->teamId);
        $responseData = $this->effortService->getEffortPerTeam($teamIds, $params);
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
