<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Services\ResourceService;

class CheckResource
{
    protected $resourceService;

    public function __construct(ResourceService $resourceService)
    {
        $this->resourceService = $resourceService;
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
        $resource = $this->resourceService->find($request->resourceId);
        if (is_null($resource)) {
            return abort(404, trans('http.404'));
        }
        if (is_null($request->projectId)) {
            $request->projectId = $resource->project->id;
        }
        
        return $next($request);
    }
}
