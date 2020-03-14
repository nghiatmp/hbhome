<?php

namespace App\Services;

use App\Enums\TeamRole;
use App\Models\UserTeam;
use App\Models\User;
use App\Services\BusyRateService;
use Carbon\Carbon;

class UserTeamService
{
    private $userTeam;
    private $user;
    private $busyRateService;

    public function __construct(UserTeam $userTeam, User $user, BusyRateService $busyRateService)
    {
        $this->userTeam = $userTeam;
        $this->user = $user;
        $this->busyRateService = $busyRateService;
    }

    public function find($teamId, $userId)
    {
        return $this->userTeam->where('team_id', $teamId)->where('user_id', $userId)->first();
    }

    public function findById($id)
    {
        return $this->userTeam->find($id);
    }

    public function countMember($teamId)
    {
        return $this->userTeam->where('team_id', $teamId)->count();
    }

    /**
     * @param $teamIds
     * @return mixed
     */
    public function countMemberByTeamIds($teamIds)
    {
        return $this->userTeam->whereIn('team_id', $teamIds)->count();
    }


    /**
     * @param integer $teamId
     * @param ['from', 'to'] $params
     *
     */
    public function index($teamId, $params)
    {
        $members = $this->userTeam
            ->with('user:id,full_name,email')
            ->where('team_id', $teamId)
            ->whereNull('to')
            ->get(['id', 'user_id', 'role']);

        return $this->busyRateService->getMemberBusyRate($members, $params);
    }

    /**
     * @param integer $teamId
     * @param ['userIds'] $params
     *
     */
    public function store($teamId, $params)
    {
        $members = array();
        foreach ($params['userIds'] as $userId) {
            if (!is_null($this->user->find($userId))) {
                $member = $this->userTeam->firstOrCreate([
                    'team_id' => $teamId,
                    'user_id' => $userId,
                    'role' => TeamRole::MEMBER,
                    'from' => Carbon::now()
                ]);
                array_push($members, $member);
            }
        }

        return $members;
    }

    /**
     * @param $parentTeamIds
     * @param $params
     * @return array
     */
    public function findByParentTeamIds($parentTeamIds, $params)
    {

        $members = $this->userTeam
            ->with('user:id,full_name,email')
            ->whereIn('team_id', $parentTeamIds)
            ->whereNull('to')
            ->get(['id', 'user_id', 'role']);

        return $this->busyRateService->getMemberBusyRate($members, $params);
    }

    /**
     * @param integer $memberId
     *
     */
    public function destroy($memberId)
    {
        //Destroy team member (update to is now())
        return $this->userTeam
            ->where('id', $memberId)
            ->update(['to' => Carbon::now()]);
    }

    /**
     * @param integer $memberId
     * @param ['role'] $params
     *
     */
    public function update($memberId, $params)
    {
        $member = $this->userTeam->find($memberId);
        $member->update($params);

        return $member;
    }

    /**
     * @param $teamIDs
     * @return mixed
     */
    public function findByTeamIDs($teamIDs)
    {
        return $this->userTeam->whereIn('team_id', $teamIDs)
            ->pluck('user_id')
            ->toArray();
    }

    /**
     * @param $userID
     * @param $from
     * @return array
     */
    public function findByDuration($userID, $from)
    {
        $dataReturn = [
            'team_id' => 0,
            'from' => $from,
            'to' => null
        ];
        $userTeamItem = $this->userTeam->where('user_id', $userID)
            ->where('from', '<=', $from)
            ->whereNull('to')
            ->first();
        if (isset($userTeamItem)) {
            return [
                'team_id' => $userTeamItem->team_id,
                'from' => $from,
                'to' => null
            ];
        }
        $userTeamItem = $this->userTeam->where('user_id', $userID)
            ->where('from', '<=', $from)
            ->orderBy('from', 'DESC')
            ->first();
        if (isset($userTeamItem) && strtotime($userTeamItem->to) >= strtotime($from)) {
            $dataReturn = [
                'team_id' => $userTeamItem->team_id,
                'from' => $from,
                'to' => $userTeamItem->to
            ];
        } else {
            $userTeamItem = $this->userTeam->where('user_id', $userID)
                ->whereNull('to')
                ->first();
            if (isset($userTeamItem)) {
                $dataReturn = [
                    'team_id' => $userTeamItem->team_id,
                    'from' => $from,
                    'to' => null
                ];
            }
        }
        return $dataReturn;
    }

    /**
     * @param $userID
     * @param $from
     * @param $to
     * @return array
     */
    public function findTeamAndDurationByDuration($userID, $from, $to)
    {
        $dataReturn = [];
        $isCheckTeam = true;
        while ($isCheckTeam && $from < $to) {
            $team = $this->findByDuration($userID, $from);
            if (!empty($team['to'])) {
                $from = Carbon::parse($team['to'])->addDays(1)->format('Y-m-d');
            } else {
                $isCheckTeam = false;
            }
            array_push($dataReturn, $team);
        }
        return $dataReturn;
    }

    /**
     * @param array $userIDs
     * @return array
     */
    public function validateDataStore(array $userIDs)
    {
        return $this->userTeam->whereIn('user_id', $userIDs)
            ->whereNull('to')->count();
    }
}
