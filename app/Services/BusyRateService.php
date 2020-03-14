<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\UserTeam;
use App\Services\HolidayService;
use App\Services\ResourceService;
use App\Helpers\Time;

class BusyRateService
{
    private $userTeam;
    private $holidayService;
    private $resourceService;

    public function __construct(UserTeam $userTeam, HolidayService $holidayService, ResourceService $resourceService)
    {
        $this->userTeam = $userTeam;
        $this->holidayService = $holidayService;
        $this->resourceService = $resourceService;
    }
    /**
     * @param array $teamIds
     *
     * @return array
     */
    public function getTeamBusyRate(array $teamIds)
    {
        $userIds = $this->userTeam->whereIn('team_id', $teamIds)->whereNull('to')->pluck('user_id');
        $userNumber = count($userIds);
        if ($userNumber === 0) {
            return [
                'from' => null,
                'to' => null,
                'busyRates' => null,
            ];
        }

        $from = Carbon::now()->startOfMonth()->subMonths(3);
        $to = Carbon::now()->startOfMonth()->addMonths(3)->endOfMonth();
        $holidays = $this->holidayService->getDates();
        $resources = $this->resourceService->getResourcesByUsers($userIds, $from, $to);

        $busyRates = array();
        $loopFrom = Carbon::parse($from);
        while ($loopFrom->lt($to)) {
            $month = $loopFrom->format('Y-m');
            $busyRate = 0;
            foreach ($resources as $resource) {
                $start = Carbon::parse($loopFrom);
                $end = Carbon::parse($loopFrom)->endOfMonth();
                $busyRate += $this->getBusyRate($start, $end, $resource, $holidays);
            }
            $busyRates[$month] = round($busyRate / $userNumber, 2);
            $loopFrom->startOfMonth()->addMonth();
        }

        return [
            'from' => $from->format('Y-m'),
            'to' => $to->format('Y-m'),
            'busyRates' => $busyRates,
        ];
    }

    /**
     * @param User[] $members
     * @param ['from', 'to'] $params
     *
     */
    public function getMemberBusyRate($members, $params)
    {
        if (is_null($params['from'])) {
            $from = Carbon::now()->startOfMonth();
            $to = Carbon::now()->endOfMonth();
        } else {
            list($from, $to) = Time::parseToCarbon(array($params['from'], $params['to']));
            $to->endOfDay();
        }
        $holidays = $this->holidayService->getDates();

        foreach ($members as $member) {
            $resources = $this->resourceService->getResourcesByUsers(array($member->user_id), $from, $to);
            $busyRate = 0;
            foreach ($resources as $resource) {
                $busyRate += $this->getBusyRate($from, $to, $resource, $holidays);
            }
            $member->busy_rate = $busyRate;
        }

        return [
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'members' => $members,
        ];
    }

    /**
     * @param integer $userId
     * @param ['from', 'to'] $params
     *
     */
    public function getUserBusyRate($userId, $params)
    {
        if (is_null($params['from'])) {
            $from = Carbon::now()->startOfMonth()->subMonths(3);
            $to = Carbon::now()->startOfMonth()->addMonths(3)->endOfMonth();
        } else {
            list($from, $to) = Time::parseToCarbon(array($params['from'], $params['to']));
            $to->endOfDay();
        }
        $holidays = $this->holidayService->getDates();
        $resources = $this->resourceService->getResourcesByUsers(array($userId), $from, $to);

        $busyRates = array();
        $loopFrom = Carbon::parse($from);
        while ($loopFrom->lt($to)) {
            $month = $loopFrom->format('Y-m');
            $busyRate = 0;
            foreach ($resources as $resource) {
                $start = Carbon::parse($loopFrom);
                $end = Carbon::parse($loopFrom)->endOfMonth();
                $busyRate += $this->getBusyRate($start, $end, $resource, $holidays);
            }
            $busyRates[$month] = $busyRate;
            $loopFrom->startOfMonth()->addMonth();
        }

        return [
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'busyRates' => $busyRates,
        ];
    }

    /**
     * @param Resource $resource
     * @param array $holidays
     * @param Date $from
     * @param Date $to
     *
     */
    protected function getBusyRate($from, $to, $resource, $holidays)
    {
        list($workFrom, $workTo) = Time::getRange(array($from, $to), array($resource->from_at, $resource->to_at));
        if (is_null($workFrom)) {
            return 0;
        }

        $workDays = $workFrom->diffInDaysFiltered(function (Carbon $date) use ($holidays) {
            return $date->isWeekday() && !in_array($date, $holidays);
        }, $workTo);

        $totalDays = $from->diffInDaysFiltered(function (Carbon $date) use ($holidays) {
            return $date->isWeekday() && !in_array($date, $holidays);
        }, $to);

        return $totalDays === 0 ? 0 : round($workDays / $totalDays * $resource->allocation, 2);
    }
}
