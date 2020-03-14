<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\ProjectUserService;

class CheckProjectMember
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
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $member = $this->projectUserService->findById($request->projectMemberId);
        if (is_null($member)) {
            return abort(404, trans('http.404'));
        }
        if (is_null($request->projectId)) {
            $request->projectId = $member->project_id;
        }
        
        return $next($request);
    }
}
