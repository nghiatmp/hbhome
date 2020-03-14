<?php

namespace App\Http\Middleware;

use App\Enums\SystemRole;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckSystemRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        $systemRole = Auth::user()->role;
        $systemRoleString = $this->getRoleString($systemRole);

        if (strpos($role, strtoupper($systemRoleString)) === false) {
            return abort(403, trans('http.403'));
        }

        return $next($request);
    }

    protected function getRoleString($systemRole)
    {
        return SystemRole::getKey($systemRole);
    }
}
