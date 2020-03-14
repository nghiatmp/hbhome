<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;

class CheckUser
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->user->findOrFail($request->userId);

        return $next($request);
    }
}
