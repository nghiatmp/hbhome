<?php

namespace App\Services;

use App\Models\Resource;
use Carbon\Carbon;
use App\Models\UserTeam;
use App\Services\ProjectService;
use App\Services\HolidayService;
use App\Services\ResourceService;
use App\Helpers\Time;

class EffortService
{
    private $userTeam;
    private $projectService;
    private $holidayService;
    private $resourceService;
    private $resource;

    public function __construct(
        UserTeam $userTeam,
        ProjectService $projectService,
        HolidayService $holidayService,
        ResourceService $resourceService,
        Resource $resource
    ) {
        $this->userTeam = $userTeam;
        $this->projectService = $projectService;
        $this->holidayService = $holidayService;
        $this->resourceService = $resourceService;
        $this->resource = $resource;
    }

    /**
     * @param integer $projectId
     *
     */
    public function getEE($projectId)
    {
        $project = $this->projectService->find($projectId);
        $budget = $project->budget;
        $resources = $project->resources()->get();
        $holidays = $this->holidayService->getDates();

        $effort = 0;
        foreach ($resources as $resource) {
            list($from, $to) = Time::getCurrentRange(array($resource->from_at, $resource->to_at));
            $effort += $this->getManMonth($from, $to, $resource, $holidays);
        }
        $ee = $effort == 0 ? 0 : round(100 * $budget / $effort, 2);
        $effort = round($effort, 2);
        return [
            'budget' => $budget,
            'effort' => $effort,
            'ee' => $ee,
        ];
    }

    /**
     * @param integer $projectId
     * @param ['from', 'to'] $params
     *
     */
    public function getEffortPerMonth($projectId, $params)
    {
        $project = $this->projectService->find($projectId);
        list($from, $to) = $this->getEffortRange($project, $params);
        if (is_null($from)) {
            return [
                'from' => $params['from'],
                'to' => $params['to'],
                'efforts' => [],
            ];
        }
        $resources = $project->resources()->get();
        $holidays = $this->holidayService->getDates();

        $efforts = array();
        $loopFrom = Carbon::parse($from);
        while ($loopFrom->lt($to)) {
            $month = $loopFrom->format('Y-m');
            $effort = 0;
            foreach ($resources as $resource) {
                $start = Carbon::parse($loopFrom);
                $end = Carbon::parse($loopFrom)->endOfMonth();
                if ($month === $to->format('Y-m')) {
                    $end = Carbon::parse($to);
                }
                $effort += $this->getManMonth($start, $end, $resource, $holidays);
            }
            $loopFrom->startOfMonth()->addMonth();
            $efforts[] = [
                "month" => $month,
                "mm" => round($effort, 2),
            ];
        }

        return [
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'efforts' => array_values($efforts),
        ];
    }
    public function getEffortPerMonthAllProject($params)
    {
        $totalproject = $this->projectService->total();
        $respose = [];
        foreach ($totalproject['data'] as $projectTest) {
            $project = $this->projectService->find($projectTest['id']);
            list($from, $to) = $this->getEffortRange($project, $params);
            if (is_null($from)) {
                $efforts = [];
            }
            $resources = $project->resources()->get();
            $holidays = $this->holidayService->getDates();

            $efforts = array();
            $loopFrom = Carbon::parse($from);
            while ($loopFrom->lt($to)) {
                $month = $loopFrom->format('m-Y');
                $effort = 0;
                foreach ($resources as $resource) {
                    $start = Carbon::parse($loopFrom);
                    $end = Carbon::parse($loopFrom)->endOfMonth();
                    if ($month === $to->format('m-Y')) {
                        $end = Carbon::parse($to);
                    }
                    $effort += $this->getManMonth($start, $end, $resource, $holidays);
                }
                $loopFrom->startOfMonth()->addMonth();
//                $efforts[] = [
//                    $month => round($effort, 2),
//                ];
                $respose[$project->title][$month] = round($effort, 2);
            }
//            $respose[$project->title]= $efforts;
        }
        return $respose;
    }

    /**
     * @param integer $projectId
     * @param ['from', 'to'] $params
     *
     */
    public function getEffortPerMember($projectId, $params)
    {
        $project = $this->projectService->find($projectId);
        list($from, $to) = $this->getEffortRange($project, $params);
        if (is_null($from)) {
            return [
                'from' => $params['from'],
                'to' => $params['to'],
                'users' => [],
            ];
        }
        $resources = $project->resources()->with('user:id,full_name,email')->get();
        $holidays = $this->holidayService->getDates();

        $users = array();
        foreach ($resources as $resource) {
            $userId = $resource->user_id;
            if (!array_key_exists($userId, $users)) {
                $users[$userId] = [
                    'id' => $resource->user->id,
                    'name' => $resource->user->full_name,
                    'email' => $resource->user->email,
                    'effort' => 0,
                ];
            }
            $users[$userId]['effort'] += $this->getManMonth($from, $to, $resource, $holidays);
            $users[$userId]['effort'] = round($users[$userId]['effort'], 2);
        }

        return [
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'users' => array_values($users),
        ];
    }

    /**
     * @param Phase $phase
     *
     */
    public function getEffortPerPhase($phase)
    {
        $project = $this->projectService->find($phase->project_id);
        $resources = $project->resources()->get();
        $holidays = $this->holidayService->getDates();

        $effort = 0;
        $planEffort = 0;
        foreach ($resources as $resource) {
            list($from, $to) = Time::getCurrentRange(array($phase->from_at, $phase->to_at));
            list($start, $end) = Time::parseToCarbon(array($phase->from_at, $phase->to_at));
            $effort += $this->getManMonth($from, $to, $resource, $holidays);
            $planEffort += $this->getManMonth($start, $end, $resource, $holidays);
        }

        return [
            'used_effort' => round($effort, 2),
            'plan_effort' => round($planEffort, 2),
        ];
    }

    /**
     * @param array $teamIds
     * @param ['from', 'to'] $params
     * @return array
     */
    public function getEffortPerTeam(array $teamIds, $params)
    {
        if (is_null($params['from'])) {
            $from = Carbon::now()->startOfMonth()->subMonths(3);
            $to = Carbon::now()->startOfMonth()->addMonths(3)->endOfMonth();
        } else {
            list($from, $to) = Time::parseToCarbon(array($params['from'], $params['to']));
            $to->endOfDay();
        }
        $userIds = $this->userTeam->whereIn('team_id', $teamIds)->whereNull('to')->pluck('user_id');
        $resources = $this->resourceService->getResourcesByUsers($userIds, $from, $to);
        $holidays = $this->holidayService->getDates();

        $efforts = array();
        $loopFrom = Carbon::parse($from);
        while ($loopFrom->lt($to)) {
            $month = $loopFrom->format('Y-m');
            $effort = 0;
            foreach ($resources as $resource) {
                $start = Carbon::parse($loopFrom);
                $end = Carbon::parse($loopFrom)->endOfMonth();
                if ($month === $to->format('Y-m')) {
                    $end = Carbon::parse($to);
                }
                $effort += $this->getManMonth($start, $end, $resource, $holidays);
            }
            $efforts[] = [
                "month" => $month,
                "mm" => round($effort, 2),
            ];
            $loopFrom->startOfMonth()->addMonth();
        }

        return [
            'from' => $from->format('Y-m'),
            'to' => $to->format('Y-m'),
            'efforts' => array_values($efforts),
        ];
    }

    /**
     * @param int $userId
     * @param ['from', 'to'] $params
     * @return array
     */
    public function getEffortPerUser($userId, $params)
    {
        if (is_null($params['from'])) {
            $from = Carbon::now()->startOfMonth()->subMonths(3);
            $to = Carbon::now()->startOfMonth()->addMonths(3)->endOfMonth();
        } else {
            list($from, $to) = Time::parseToCarbon(array($params['from'], $params['to']));
            $to->endOfDay();
        }

        $resources = $this->resourceService->getResourcesByUsers([$userId], $from, $to);
        $holidays = $this->holidayService->getDates();

        $efforts = array();
        $loopFrom = Carbon::parse($from);
        while ($loopFrom->lt($to)) {
            $month = $loopFrom->format('Y-m');
            $effort = 0;
            foreach ($resources as $resource) {
                $start = Carbon::parse($loopFrom);
                $end = Carbon::parse($loopFrom)->endOfMonth();
                if ($month === $to->format('Y-m')) {
                    $end = Carbon::parse($to);
                }
                $effort += $this->getManMonth($start, $end, $resource, $holidays);
            }
            $efforts[] = [
                'month' => $month,
                'mm' => round($effort, 2),
            ];
            $loopFrom->startOfMonth()->addMonth();
        }

        return [
            'from' => $from->format('Y-m'),
            'to' => $to->format('Y-m'),
            'efforts' => array_values($efforts),
        ];
    }

    /**
     * @param Date $from
     * @param Date $to
     *
     * @param Resource $resource
     * @param array $holidays
     * @param null $manMonth
     * @return float|int
     */
    public function getManMonth($from, $to, $resource, $holidays, $manMonth = null)
    {
        if (is_array($resource)) {
            list($start, $end) = Time::getRange(array($from, $to), array($resource['from_at'], $resource['to_at']));
            $allocation = $resource['allocation'];
        } else {
            list($start, $end) = Time::getRange(array($from, $to), array($resource->from_at, $resource->to_at));
            $allocation = $resource->allocation;
        }
        if (is_null($start)) {
            return 0;
        }
        $days = $start->diffInDaysFiltered(function (Carbon $date) use ($holidays) {
            return $date->isWeekday() && !in_array($date, $holidays);
        }, $end);
        if (empty($manMonth)) {
            $manMonth = config('app.man_month');
        }
        return $days * ($allocation / 100) / $manMonth;
    }

    /**
     * @param ['from', 'to'] $range
     * @param Project $project
     *
     */
    protected function getEffortRange($project, $range)
    {
        if (is_null($project->from_at)) {
            return null;
        }

        $projectRange = array($project->from_at, $project->to_at);
        $searchRange = array($range['from'], $range['to']);
        if (is_null($range['from'])) {
            list($from, $to) = Time::parseToCarbon($projectRange);
            $to->endOfDay();
        } else {
            list($from, $to) = Time::getRange($projectRange, $searchRange);
        }

        return array($from, $to);
    }

    /**
     * @param $from
     * @param $to
     * @param $holidays
     * @return array
     */
    public function findResourceNotEnoughEffortByDuration($from, $to, $holidays)
    {
        $resourceNotEnoughAllocation = $this->resource
            ->where('from_at', '<=', $to)
            ->where('to_at', '>=', $from)
            ->get()
            ->toArray();
        $dataResource = [];
        $manMonth = $from->diffInDaysFiltered(function (Carbon $date) use ($holidays) {
            return $date->isWeekday() && !in_array($date, $holidays);
        }, $to);
        $from = Carbon::parse($from)->format(config('constant.DATE_FORMAT'));
        $to = Carbon::parse($to)->format(config('constant.DATE_FORMAT'));
        foreach ($resourceNotEnoughAllocation as $resource) {
            $userID = $resource['user_id'];
            if (!isset($dataResource[$userID])) {
                $dataResource[$userID] = 0;
            }
            $effort = $this->getManMonth($from, $to, $resource, $holidays, $manMonth);
            $dataResource[$userID] += $effort;
        }
        $dataReturn = [];
        foreach ($dataResource as $key => $value) {
            if ($value < 1) {
                array_push($dataReturn, $key);
            }
        }
        return $dataReturn;
    }
}
