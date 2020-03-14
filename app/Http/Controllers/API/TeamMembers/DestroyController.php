<?php

namespace App\Http\Controllers\API\TeamMembers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserTeamService;

/**
 * @group Team Members
 */
class DestroyController extends Controller
{
    protected $userTeamService;

    public function __construct(UserTeamService $userTeamService)
    {
        $this->userTeamService = $userTeamService;
    }

    /**
     * Delete
     * Delete team member
     *
     * @bodyParam teamMemberId integer required
     *
     * @return null
     *
     * @response 204 {}
     */
    public function main(Request $request)
    {
        $this->userTeamService->destroy($request->teamMemberId);
        return response()->json([], 204);
    }
}
