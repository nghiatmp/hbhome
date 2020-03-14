<?php

namespace App\Http\Controllers\API\Teams;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use Illuminate\Http\Request;
use App\Services\BusyRateService;

/**
 * @group Teams
 */
class BusyRateController extends Controller
{
    protected $busyRateService;
    protected $teamService;

    public function __construct(BusyRateService $busyRateService, TeamService $teamService)
    {
        $this->busyRateService = $busyRateService;
        $this->teamService = $teamService;
    }

    /**
     * Busy rate
     * Get team's busy rate per month
     *
     * @bodyParam teamId integer required
     *
     * @return Object
     *
     * @response {
     *  "from": "2019-01-01",
     *  "to": "2019-07-31",
     *  "busyRates": {
     *      "2019-01": 17,
     *      "2019-02": 43,
     *      "2019-03": 32,
     *      "2019-04": 88,
     *      "2019-05": 89,
     *      "2019-06": 90,
     *      "2019-07": 36
     *  }
     * }
     */
    public function main(Request $request)
    {
        //get all teamId children
        $teamIds = $this->teamService->getAllChildrenTeam($request->teamId);
        $responseData = $this->busyRateService->getTeamBusyRate($teamIds);
        return response()->json($responseData, 200);
    }
}
