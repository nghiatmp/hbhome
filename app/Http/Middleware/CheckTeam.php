<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\TeamService;

class CheckTeam
{
    protected $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
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
        $team = $this->teamService->find($request->teamId);
        if (is_null($team)) {
            return abort(404, trans('http.404'));
        }
        
        return $next($request);
    }
}
