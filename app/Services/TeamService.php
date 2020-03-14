<?php

namespace App\Services;

use App\Models\Team;

class TeamService
{
    private $team;
    private $userTeamService;

    public function __construct(Team $team, UserTeamService $userTeamService)
    {
        $this->team = $team;
        $this->userTeamService = $userTeamService;
    }

    /**
     * @param integer $id
     *
     */
    public function find($id)
    {
        return $this->team->find($id);
    }

    /**
     * @param integer $id
     * @param ['title'] $params
     *
     */
    public function update($id, $params)
    {
        $teamObject = $this->team->find($id);
        $teamObject->update($params);

        return $teamObject;
    }

    /**
     * @param ['title'] $params
     *
     */
    public function store($params)
    {
        return $this->team->create($params);
    }

    /**
     * @param $idsHidden
     * @return array teams
     */
    public function index($idsHidden)
    {
        $teams = $this->team
            ->whereNotIn('id', $idsHidden)
            ->get(['id', 'parent_id', 'title'])->toArray();

        return [
            'data' => $teams
        ];
    }

    /**
     * @param integer $teamId
     *
     */
    public function destroy($teamId)
    {
        $teamIds = $this->getAllChildrenTeam($teamId);
        $memberCount = $this->userTeamService->countMemberByTeamIds($teamIds);
        if ($memberCount != 0) {
            return abort(400, 'This team is not empty');
        }
        return $this->destroyByTeamIds($teamIds);
    }

    /**
     * @param $teamId
     * @return array
     */
    public function getAllChildrenTeam($teamId)
    {
        $allTeams = $this->index([])['data'];
        return $this->getAllChildrenTeamIds($allTeams, $teamId, false, []);
    }

    /**
     * @param $allTeams
     * @param $teamId
     * @param bool $parentFound
     * @param array $teamIds
     * @return array
     */
    public function getAllChildrenTeamIds($allTeams, $teamId, $parentFound = false, $teamIds = array())
    {
        foreach ($allTeams as $row) {
            if ((!$parentFound && $row['id'] == $teamId) || $row['parent_id'] == $teamId) {
                $teamIds[] = $row['id'];
                if ($row['parent_id'] == $teamId) {
                    $teamIds = array_merge($teamIds, $this->getAllChildrenTeamIds($allTeams, $row['id'], true));
                }
            }
        }
        return $teamIds;
    }

    /**
     * @param $teams
     * @return mixed
     */
    public function getDataTeamForTreeView($teams)
    {
        return $this->getChildrenOfCorporate($teams, [], config('constant.PARENT_ROOT_TEAM_ID'));
    }

    /**
     * @param $teams
     * @param $dataChildren
     * @param $parentID
     * @desc recursive function get all children of a team
     * @return mixed
     */
    public function getChildrenOfCorporate($teams, $dataChildren, $parentID)
    {
        if (count($teams) == 0) {
            return $dataChildren;
        }
        foreach ($teams as $key => $team) {
            if ($team['parent_id'] == $parentID) {
                $item = [
                    'id' => $team['id'],
                    'title' => $team['title'],
                    'children' => []
                ];
                unset($teams[$key]);
                $item['children'] = $this->getChildrenOfCorporate($teams, $item['children'], $item['id']);
                array_push($dataChildren, $item);
            }
        }
        return $dataChildren;
    }

    /**
     * @param $teamIds
     * @return mixed
     */
    public function destroyByTeamIds($teamIds)
    {
        return $this->team->whereIn('id', $teamIds)->delete();
    }

    /**
     * @return array
     */
    public function getAllTeam()
    {
        return Team::all()->toArray();
    }

    /**
     * @param $allTeams
     * @param $teamId
     * @param bool $parentFound
     * @param array $childrenTeam
     * @return array
     */
    public function getAllChildrenTeamTree($allTeams, $teamId, $parentFound = false, $childrenTeam = array())
    {
        foreach ($allTeams as $row) {
            if ((!$parentFound && $row['id'] == $teamId) || $row['parent_id'] == $teamId) {
                if ($row['id'] == $teamId) {
                    $parent = $row;
                } else {
                    $childrenTeam[] = $row;
                    if ($row['parent_id'] == $teamId) {
                        $childrenTeam =
                            array_merge($childrenTeam, $this->getAllChildrenTeamIds($allTeams, $row['id'], true));
                    }
                }
            }
        }
        $childrenTeam = $this->getChildrenOfCorporate($childrenTeam, [], $teamId);
        if (isset($parent)) {
            return [
                'id' => $parent['id'],
                'title' => $parent['title'],
                'children' => $childrenTeam
            ];
        } else {
            return [];
        }
    }
}
