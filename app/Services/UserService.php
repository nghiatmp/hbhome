<?php

namespace App\Services;

use App\Enums\TeamRole;
use App\Models\User;
use App\Models\UserTeam;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserService
{
    private $user;
    private $userTeam;

    public function __construct(User $user, UserTeam $userTeam)
    {
        $this->user = $user;
        $this->userTeam = $userTeam;
    }

    /**
     *
     * @return list users data
     */
    public function index()
    {
        $columns = ['id', 'full_name', 'email', 'role'];
        $users = $this->user->paginate(20, $columns, 'page')->toArray();

        return [
            'data' => $users['data'],
            'current_page' => $users['current_page'],
            'per_page' => $users['per_page'],
            'last_page' => $users['last_page'],
            'total' => $users['total'],
        ];
    }

    /**
     * @param ['full_name', 'role', 'email'] $params
     *
     */
    public function store($params)
    {
        if (isset($params['team_id'])) {
            $teamId = $params['team_id'];
            unset($params['team_id']);
        }
        $newUser = $this->user->create($params);
        if (isset($teamId)) {
            $this->userTeam->firstOrCreate([
                'team_id' => $teamId,
                'user_id' => $newUser->id,
                'role' => TeamRole::MEMBER,
                'from' => Carbon::now()
            ]);
        }
        return $newUser;
    }

    /**
     * @param integer $id
     * @param ['full_name', 'email', 'role', 'team_id'] $params
     *
     */
    public function update($id, $params)
    {
        return DB::transaction(function () use ($id, $params) {
            $userObject = $this->user->findOrFail($id);
            $userObject->update($params);
            $currentUserTeam = $this->userTeam->where('user_id', $id)->whereNull('to')->first();
            if (!is_null($currentUserTeam)) {
                if (!array_key_exists('team_id', $params)
                    || $params['team_id'] != $currentUserTeam['team_id']) {
                    $this->userTeam
                        ->where('id', $currentUserTeam->id)
                        ->update(['to' => Carbon::now()]);
                }
                if (array_key_exists('team_id', $params) && $params['team_id'] != $currentUserTeam['team_id']) {
                    $this->userTeam->create([
                        'team_id' => $params['team_id'],
                        'user_id' => $id,
                        'role' => $params['role'],
                        'from' => Carbon::now()
                    ]);
                }
                return $userObject;
            }
            if (array_key_exists('team_id', $params)) {
                $this->userTeam->create([
                    'team_id' => $params['team_id'],
                    'user_id' => $id,
                    'role' => $params['role'],
                    'from' => Carbon::now()
                ]);
            }

            return $userObject;
        });
    }

    /**
     * @param ['keyword', 'sortType', 'orderBy'] $params
     *
     */
    public function search($params)
    {
        $columns = ['id', 'full_name', 'email', 'role'];
        $keyword = $params['keyword'];
        $sortType = $params['sortType'];
        $orderBy = $params['orderBy'];
        $users = $this->user
            ->when(isset($keyword), function ($query) use ($keyword) {
                $query->where('email', 'like', $keyword . '%');
            })
            ->orderBy($orderBy, $sortType)
            ->with('availableTeam')
            ->paginate(100, $columns, 'page')
            ->toArray();

        return [
            'data' => $users['data'],
            'keyword' => $keyword,
            'sort_type' => $sortType,
            'order_by' => $orderBy,
            'current_page' => $users['current_page'],
            'per_page' => $users['per_page'],
            'last_page' => $users['last_page'],
            'total' => $users['total'],
        ];
    }

    /**
     * @param ['keyword'] $params
     *
     */
    public function suggest($params)
    {
        $columns = ['id', 'full_name', 'email'];
        $keyword = $params['keyword'];
        $users = $this->user
            ->where('email', 'like', '%' . $keyword . '%')
            ->orderBy('id', 'desc')
            ->limit(6)
            ->get($columns)
            ->toArray();

        return [
            'data' => $users,
        ];
    }

    /**
     * @param ['email'] $params
     *
     */
    public function emailExists($params)
    {
        $email = $params['email'];
        return [
            'has_email' => $this->user->where('email', $email)->exists(),
        ];
    }

    /**
     * @param $userIDs
     * @return mixed
     */
    public function findNotInUserIDs($userIDs)
    {
        return $this->user->whereNotIn('id', $userIDs)->get()->toArray();
    }

    public function getAllUser()
    {
        return $this->user->get();
    }
}
