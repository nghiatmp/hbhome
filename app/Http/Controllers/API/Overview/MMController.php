<?php

namespace App\Http\Controllers\API\Overview;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Services\HolidayService;
use App\Services\OverviewService;
use App\Services\TeamService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MMController extends Controller
{
    protected $overviewService;
    protected $teamService;
    protected $holidayService;
    protected $resources;

    /**
     * MMController constructor.
     * @param OverviewService $overviewService
     * @param TeamService $teamService
     * @param HolidayService $holidayService
     * @param Resource $resource
     */
    public function __construct(
        OverviewService $overviewService,
        TeamService $teamService,
        HolidayService $holidayService,
        Resource $resource
    ) {
        $this->overviewService = $overviewService;
        $this->teamService = $teamService;
        $this->holidayService = $holidayService;
        $this->resources = $resource;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function main(Request $request)
    {
        $data = $request->all();
        $teamID = $data['team_id'] ?? config('constant.PARENT_ROOT_TEAM_ID');
        $from = $data['from'] ?? null;
        $to = $data['to'] ?? null;
        $from = Carbon::parse($from)->startOfMonth();
        $to = Carbon::parse($to)->endOfMonth();

        if (strtotime($to) < strtotime($from)) {
            return response()->json([], 200);
        }
        //Get team by teamID
        $allTeam = $this->teamService->getAllTeam();

        if ($teamID == config('constant.PARENT_ROOT_TEAM_ID')) {
            $treeTeam = [
                'id' => 0,
                'title' => 'Corporate',
                'children' => $this->teamService->getDataTeamForTreeView($allTeam)
            ];
        } else {
            $treeTeam = $this->teamService->getAllChildrenTeamTree($allTeam, $teamID);
        }
        //All team are child of team parent
        $childrenTeamIDs = $this->teamService->getAllChildrenTeam($teamID);

        //Get all holidays by duration
        $holidays = $this->holidayService->findHolidaysByDuration($from, $to);

        //get resource by team and duration
        $dataOverview = $this->overviewService
            ->getMMByTeamAndDuration($from, $to, $holidays, $treeTeam, $childrenTeamIDs);
        return response()->json($dataOverview, 200);
    }
}
