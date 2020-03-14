<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\UserTeamService;

class CheckTeamMember
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
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $member = $this->userTeamService->findById($request->teamMemberId);
        if (is_null($member)) {
            return abort(404, trans('http.404'));
        }
        if (is_null($request->teamId)) {
            $request->teamId = $member->team_id;
        }

        return $next($request);
    }
}
