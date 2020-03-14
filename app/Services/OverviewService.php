<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Arr;

class OverviewService
{
    protected $resourceService;
    protected $userTeamService;
    protected $effortService;
    protected $budgetDetailService;

    public function __construct(
        ResourceService $resourceService,
        UserTeamService $userTeamService,
        EffortService $effortService,
        BudgetDetailService $budgetDetailService
    ) {
        $this->resourceService = $resourceService;
        $this->userTeamService = $userTeamService;
        $this->effortService = $effortService;
        $this->budgetDetailService = $budgetDetailService;
    }

    /**
     * @param $dataTree
     * @param $field
     * @return array
     */
    public function calculateDataTree($dataTree, $field)
    {
        $dataReturn = [];
        foreach ($dataTree as $data) {
            array_push($dataReturn, $this->sumTotalDataTree($data, $field));
        }
        return $dataReturn;
    }

    /**
     * @param $data
     * @param $field
     * @return mixed
     */
    public function sumTotalDataTree($data, $field)
    {
        if (count($data['children']) == 0) {
            $data[$field] = $data[$field] ?? 0;
            return $data;
        }

        if (!isset($data[$field])) {
            $data[$field] = 0;
        }

        $dataChildren = [];
        foreach ($data['children'] as $item) {
            $sumArr = $this->sumTotalDataTree($item, $field);
            array_push($dataChildren, $sumArr);
            $data[$field] = round($data[$field] + $sumArr[$field], 2);
        }

        $data['children'] = $dataChildren;

        return $data;
    }

    /**
     * @param $from
     * @param $to
     * @param $holidays
     * @param $treeTeam
     * @param $childrenTeamIDs
     * @return array
     */
    public function getMMByTeamAndDuration($from, $to, $holidays, $treeTeam, $childrenTeamIDs)
    {
        $userIDs = $this->userTeamService->findByTeamIDs($childrenTeamIDs);

        $resources = $this->resourceService->findByUserIdsAndDuration($userIDs, $from, $to);

        //format tree team with key is team_id
        $treeTeam = $this->formatTreeTeamWithKeyById($treeTeam);

        $resourcesWithMM = $this->calculateMMByResourcesHolidayAndDuration($resources, $holidays, $to);

        $dataMM =  $this->formatDataMMByTreeTeamAndResources($treeTeam, $resourcesWithMM);

        if (isset($dataMM['children'])) {
            $dataMM['children'] = $this->calculateDataTree($dataMM['children'], 'mm');
            if (!isset($dataMM['mm'])) {
                $dataMM['mm'] = 0;
            }
            if (count($dataMM['children']) > 0) {
                foreach ($dataMM['children'] as $item) {
                    $dataMM['mm'] += $item['mm'];
                }
            }
        }
        return $dataMM;
    }

    /**
     * @param $treeTeam
     * @param $resources
     * @return mixed
     */
    public function formatDataMMByTreeTeamAndResources($treeTeam, $resources)
    {
        if (isset($resources[$treeTeam['id']])) {
            $treeTeam['members'] = $resources[$treeTeam['id']]['members'];
            $treeTeam['mm'] = round($resources[$treeTeam['id']]['mm'], 2);
        }

        if (isset($treeTeam['members']) && $treeTeam['members'] > 0) {
            foreach ($treeTeam['members'] as $key => $member) {
                $treeTeam['members'][$key]['mm'] = round($treeTeam['members'][$key]['mm'], 2);
            }
        }
        if (count($treeTeam['children']) == 0) {
            return $treeTeam;
        }
        foreach ($treeTeam['children'] as $key => $child) {
            if (isset($resources[$key])) {
                $treeTeam['children'][$key]['members'] = $resources[$key]['members'];
                $treeTeam['children'][$key]['mm'] = round($resources[$key]['mm'], 2);
            }
            if (isset($child['children'])) {
                $treeTeam['children'][$key] =
                    $this->formatDataMMByTreeTeamAndResources($treeTeam['children'][$key], $resources);
            }
        }
        return $treeTeam;
    }

    /**
     *
     *
     * @param $resources
     * @param $holidays
     * @param $to
     * @return array
     */
    public function calculateMMByResourcesHolidayAndDuration($resources, $holidays, $to)
    {
        $dataReturn = [];
        foreach ($resources as $resource) {
            $teamItems = $this->userTeamService
                ->findTeamAndDurationByDuration($resource['user_id'], $resource['from_at'], $resource['to_at']);
            foreach ($teamItems as $teamItem) {
                $teamId = $teamItem['team_id'];
                $fromResource = $teamItem['from'];
                $toResource = !empty($teamItem['to']) ? $teamItem['to'] : $to;
                if (!array_key_exists($teamId, $dataReturn)) {
                    $dataReturn[$teamId] = [
                        "members" => [],
                        "mm" => 0
                    ];
                }
                $keyResourceItem = array_search(
                    $resource['user_id'],
                    array_column($dataReturn[$teamId]['members'], 'user_id')
                );
                if ($keyResourceItem || (!$keyResourceItem &&
                        isset($dataReturn[$teamId]['members'][0])
                        && $dataReturn[$teamId]['members'][0]['user_id'] == $resource['user_id'])) {
                    $mmItem = $this->effortService->getManMonth($fromResource, $toResource, $resource, $holidays);
                    $dataReturn[$teamId]['members'][$keyResourceItem]['mm'] += $mmItem;
                    $dataReturn[$teamId]['mm'] += $mmItem;
                } else {
                    $resourceItem = [
                        'user_id' => $resource['user_id'],
                        'full_name' => $resource['user']['full_name'],
                        'email' => $resource['user']['email'],
                        'team_id' => $teamId,
                        'mm' => $this->effortService->getManMonth($fromResource, $toResource, $resource, $holidays)
                    ];
                    array_push($dataReturn[$teamId]['members'], $resourceItem);
                    $dataReturn[$teamId]['mm'] += $resourceItem['mm'];
                }
            }
        }
        return $dataReturn;
    }

    /**
     * @param $month
     * @param $holidays
     * @param $treeTeam
     * @param $allTeams
     * @return array
     */
    public function getEEByMonth($month, $holidays, $treeTeam, $allTeams)
    {
        $from = Carbon::parse($month)->startOfMonth();
        $to = Carbon::parse($month)->endOfMonth();
        $resourcesFree = $this->resourceService->findResourceFreeByDuration($from, $to);
        //get All resources by duration
        $resources = $this->resourceService->findByDuration($from, $to);
        $arrayTeams = Arr::pluck($allTeams, 'title', 'id');
        $userIDFree = $this->effortService->findResourceNotEnoughEffortByDuration($from, $to, $holidays);
        $dataEEByProject = $this->getEEForProject($resources, $arrayTeams, $month, $userIDFree, $holidays);
        $dataEEByProject = $this->getEEByTeam($dataEEByProject, $treeTeam);
        $dataOverview = $this->addResourceFree($dataEEByProject, $resourcesFree);
        return $dataOverview[0] ?? [];
    }

    /**
     * @param $dataEE
     * @param $resourceFree
     * @return mixed
     */
    public function addResourceFree($dataEE, $resourceFree)
    {
        $resourceFreeFormat = [];
        if (isset($resourceFree['free']) && count($resourceFree['free'])) {
            foreach ($resourceFree['free'] as $user) {
                $itemResourceFree = [
                    'id' => $user['id'],
                    'title' => $user['full_name'],
                    'email' => $user['email'],
                    'ee' => 0,
                    'flag_free' => true
                ];
                array_push($resourceFreeFormat, $itemResourceFree);
            }
        }
        array_push($dataEE[0]['children'], [
            'title' => 'Free',
            'children' => $resourceFreeFormat,
            'ee' => 0,
            'budget' => 0
        ]);
        return $dataEE;
    }

    /**
     * @param $resources
     * @param $teams
     * @param $month
     * @param $userIdsFree
     * @param $holidays
     * @return array
     * {
     *"2": { //team_id
     *   "projects" : [
     *    "7": { //project_id
     *       "resources": [
     *          {
     *              "user_id": 11,
     *              "full_name": "GL_32",
     *              "email": "gl.32@hblab.vn",
     *              "role": 15
     *              "ee": 0.8
     *           },
     *           {
     *              "user_id": 2,
     *              "full_name": "D3 - mem_d3_g32",
     *              "email": "mem_d3_g32@hblab.vn",
     *              "role": 9
     *              "ee": 1
     *           }
     *      ],
     *      "key": "PR4",
     *      "title": "Project 5",
     *      "team_id": 2,
     *      "ee": 1.8,
     *      "budget": 2
     *      }
     *  }
     *   ],
     *   "ee" : 2
     *   "budget": 2
     *}
     */
    public function getEEForProject($resources, $teams, $month, $userIdsFree, $holidays)
    {
        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();
        //get number day works on month
        $manMonth = $start->diffInDaysFiltered(function (Carbon $date) use ($holidays) {
            return $date->isWeekday() && !in_array($date, $holidays);
        }, $end);
        $start = Carbon::parse($start)->format(config('constant.DATE_FORMAT'));
        $end = Carbon::parse($end)->format(config('constant.DATE_FORMAT'));
        $dataReturn = [];
        foreach ($resources as $resource) {
            $projectID = $resource->project_id;
            $teamID = $resource->project->team_id;
            $itemInitial = [
                'resources' => [],
                'key' => $resource->project->key,
                'title' => $resource->project->title,
                'team_id' => $teamID,
                'ee' => 0,
                'budget' => 0
            ];
            //initialize $dataReturn[$teamID] when does not exist
            if (!array_key_exists($teamID, $dataReturn)) {
                $dataReturn[$teamID] = [
                    'projects' => [],
                    'ee' => 0,
                    'budget' => 0
                ];
            }
            if (!array_key_exists($projectID, $dataReturn[$teamID]['projects'])) {
                //get data budget by month of project
                $budget = $this->budgetDetailService->getBudgetByMonthAndProjectID($month, $projectID);
                $dataReturn[$teamID]['budget'] += $budget;
                $itemInitial['budget'] += $budget;
                $dataReturn[$teamID]['projects'][$projectID] = $itemInitial;
            }

            $fullName = $resource->user->full_name;
            $userTeams = $resource->user->teams;
            $flagTeam = false;
            if (!in_array($teamID, $userTeams) && isset($userTeams[0])
                && array_key_exists($userTeams[0], $teams)) {
                $fullName = $teams[$userTeams[0]] .' - '. $fullName;
                $flagTeam = true;
            }

            $itemResource = [
                'user_id' => $resource->user_id,
                'full_name' => $fullName,
                'email' => $resource->user->email,
                'role' => $resource->role,
                'ee' => round($this->effortService->getManMonth($start, $end, $resource, $holidays, $manMonth), 2),
                'flag_team' => $flagTeam,
                'flag_ee' => in_array($resource->user_id, $userIdsFree)
            ];
            $dataReturn[$teamID]['projects'][$projectID]['ee'] += $itemResource['ee'];
            $dataReturn[$teamID]['ee'] += $itemResource['ee'];
            array_push($dataReturn[$teamID]['projects'][$projectID]['resources'], $itemResource);
        }
        return $dataReturn;
    }

    /**
     * @param $eeProjects
     * @param $teams
     */
    public function getEEByTeam($eeProjects, $teams)
    {
        $dataEEForTeam = $this->addDataEEForTeam($eeProjects, $teams);
        $dataReturn[0] = [
            'title' => 'Corporate',
            'children' => $dataEEForTeam
        ];
        $dataReturn = $this->calculateDataTree($dataReturn, 'ee');
        return $this->calculateDataTree($dataReturn, 'budget');
    }

    /**
     * @param $eeProject
     * @param $teams
     * @return mixed
     */
    public function addDataEEForTeam($eeProject, $teams)
    {
        foreach ($teams as $key => $team) {
            if (isset($team['id']) && isset($eeProject[$team['id']])) {
                $teams[$key]['projects'] = $eeProject[$team['id']]['projects'];
                $teams[$key]['ee'] = round($eeProject[$team['id']]['ee'], 2);
                $teams[$key]['budget'] = round($eeProject[$team['id']]['budget'], 2);
            }
            if (isset($team['children'])) {
                $teams[$key]['children'] = $this->addDataEEForTeam($eeProject, $team['children']);
            }
        }
        return $teams;
    }

    /**
     * @param $treeTeam
     * @return mixed
     */
    public function formatTreeTeamWithKeyById($treeTeam)
    {
        if (count($treeTeam['children']) == 0) {
            $treeTeam['mm'] = 0;
            $treeTeam['members'] = [];
            return $treeTeam;
        }
        $children = [];
        foreach ($treeTeam['children'] as $child) {
            $children[$child['id']] = $this->formatTreeTeamWithKeyById($child);
        }
        $treeTeam['children'] = $children;
        $treeTeam['mm'] = 0;
        $treeTeam['members'] = [];
        return $treeTeam;
    }
}
