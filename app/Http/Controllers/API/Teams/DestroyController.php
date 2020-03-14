<?php

namespace App\Http\Controllers\API\Teams;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TeamService;

/**
 * @group Teams
 */
class DestroyController extends Controller
{
    protected $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    /**
     * Delete
     * Delete team
     *
     * @bodyParam teamId integer required
     *
     * @return null
     *
     * @response 204 {}
     *
     */
    public function main(Request $request)
    {
        $this->teamService->destroy($request->teamId);
        return response()->json([], 204);
    }
}
