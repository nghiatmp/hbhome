<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\DivisionService;

class CheckDivision
{
    protected $divisionService;

    public function __construct(DivisionService $divisionService)
    {
        $this->divisionService = $divisionService;
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
        $division = $this->divisionService->find($request->divisionId);
        if (is_null($division)) {
            return abort(404, trans('http.404'));
        }
        
        return $next($request);
    }
}
