<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Services\ProjectService;

class CheckProject
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
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
        $project = $this->projectService->find($request->projectId);
        if (is_null($project)) {
            return abort(404, trans('http.404'));
        }
        $request->project = $project;
        
        return $next($request);
    }
}
