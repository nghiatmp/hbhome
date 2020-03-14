<?php

namespace App\Http\Controllers\API\Phases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PhaseService;

/**
 * @group Phases
 */
class DestroyController extends Controller
{
    private $phaseService;

    public function __construct(PhaseService $phaseService)
    {
        $this->phaseService = $phaseService;
    }

    /**
     * Destroy
     * Destroy phase
     *
     * @bodyParam phaseId integer required
     *
     * @return null
     *
     * @response 204 {}
     *
     */
    public function main(Request $request)
    {
        $this->phaseService->destroy($request->phaseId);
        return response()->json([], 204);
    }
}
