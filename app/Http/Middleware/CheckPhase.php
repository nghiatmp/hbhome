<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Services\PhaseService;

class CheckPhase
{
    protected $phaseService;

    public function __construct(PhaseService $phaseService)
    {
        $this->phaseService = $phaseService;
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
        $phase = $this->phaseService->find($request->phaseId);
        if (is_null($phase)) {
            return abort(404, trans('http.404'));
        }
        if (is_null($request->projectId)) {
            $request->projectId = $phase->project->id;
        }
        
        return $next($request);
    }
}
