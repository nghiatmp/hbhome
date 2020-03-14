<?php

namespace App\Http\Controllers\API\Phases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PhaseService;

/**
 * @group Phases
 */
class ShowController extends Controller
{
    protected $phaseService;

    public function __construct(PhaseService $phaseService)
    {
        $this->phaseService = $phaseService;
    }
    
    /**
     * Show
     * Get phase's detail
     *
     * @bodyParam phaseId integer required
     *
     * @return Object
     *
     * @response {
     *  "id": 29,
     *  "title": "abc",
     *  "from_at": "2019-02-25",
     *  "to_at": "2019-02-25",
     *  "status": 1,
     *  "budget": "1.43",
     *  "note": "dadas",
     *  "css": null,
     *  "leakage": null,
     *  "ee": null,
     *  "timeliness": null,
     *  "project_id": 1,
     *  "created_at": "2019-02-26 02:48:00",
     *  "updated_at": "2019-03-22 09:51:21",
     *  "used_effort": 2.33
     *  "plan_effort": 2.5
     * }
     *
     * @response 403 {
     *   "message": "Forbidden",
     *   "errors": {}
     * }
     *
     * @response 404 {
     *   "message": "Not Found",
     *   "errors": {}
     * }
     */
    public function main(Request $request)
    {
        $responseData = $this->phaseService->show($request->phaseId);
        return response()->json($responseData, 200);
    }
}
