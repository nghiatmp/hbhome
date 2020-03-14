<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Enums\TeamRole;
use App\Enums\SystemRole;
use App\Services\UserTeamService;

class CheckTeamRole
{
    protected $userTeamService;

    public function __construct(UserTeamService $userTeamService)
    {
        $this->userTeamService = $userTeamService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if ($this->isSystemAdmin()) {
            return $next($request);
        }

        $userTeam = $this->userTeamService->find($request->teamId, Auth::user()->id);
        if (is_null($userTeam) || !$this->hasPermision($role, $userTeam->role)) {
            return abort(403, trans('http.403'));
        }

        return $next($request);
    }

    protected function isSystemAdmin()
    {
        return Auth::user()->role === SystemRole::ADMIN;
    }

    protected function hasPermision($role, $teamRole)
    {
        $teamRoleKey = TeamRole::getKey($teamRole);
        return strpos($role, $teamRoleKey) !== false;
    }
}
