<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Enums\SystemRole;
use App\Enums\ProjectRole;
use App\Services\ProjectUserService;

class CheckProjectRole
{
    protected $projectUserService;

    public function __construct(ProjectUserService $projectUserService)
    {
        $this->projectUserService = $projectUserService;
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

        $projectUser = $this->projectUserService->find($request->projectId, Auth::user()->id);
        if (is_null($projectUser) || !$projectUser->is_member || !$this->hasPermision($role, $projectUser->role)) {
            return abort(403, trans('http.403'));
        }

        return $next($request);
    }

    protected function isSystemAdmin()
    {
        return Auth::user()->role === SystemRole::ADMIN;
    }

    protected function hasPermision($role, $projectRole)
    {
        $projectRoleKey = $projectRole === ProjectRole::ADMIN ? ProjectRole::getKey($projectRole) : 'MEMBER';
        return strpos($role, $projectRoleKey) !== false;
    }
}
