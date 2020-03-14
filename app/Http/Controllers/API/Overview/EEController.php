<?php

namespace App\Http\Controllers\API\Overview;

use App\Http\Controllers\Controller;
use App\Services\HolidayService;
use App\Services\OverviewService;
use App\Services\TeamService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EEController extends Controller
{
    protected $overviewService;
    protected $holidayService;
    protected $teamService;

    /**
     * EEController constructor.
     * @param OverviewService $overviewService
     * @param HolidayService $holidayService
     * @param TeamService $teamService
     */
    public function __construct(
        OverviewService $overviewService,
        HolidayService $holidayService,
        TeamService $teamService
    ) {
        $this->overviewService = $overviewService;
        $this->holidayService = $holidayService;
        $this->teamService = $teamService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function main(Request $request)
    {
        $month = $request->get('month');
        $from = Carbon::parse($month)->startOfMonth();
        $to = Carbon::parse($month)->endOfMonth();
        //Get all holidays by duration
        $holidays = $this->holidayService->findHolidaysByDuration($from, $to);
        //get all team
        $allTeams = $this->teamService->getAllTeam();
        // get all team by tree
        $treeTeam = $this->teamService->getDataTeamForTreeView($allTeams);
        $dataOverview = $this->overviewService->getEEByMonth($month, $holidays, $treeTeam, $allTeams);
        return response()->json($dataOverview, 200);
    }
}
