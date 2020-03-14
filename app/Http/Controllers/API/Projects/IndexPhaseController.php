<?php

namespace App\Http\Controllers\API\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PhaseService;

/**
 * @group Projects
 */
class IndexPhaseController extends Controller
{
    protected $phaseService;

    public function __construct(PhaseService $phaseService)
    {
        $this->phaseService = $phaseService;
    }
    
    /**
     * Index phase
     * Get all phases in project
     *
     * @return Array
     *
     * @response {
     * "data": [
     *  {
     *      "id": 1,
     *      "title": "abc",
     *      "status": 1,
     *      "from_at": "2019-02-25",
     *      "to_at": "2019-02-25",
     *      "budget": "20.4",
     *      "note": "2dag0",
     *      "css": null,
     *      "leakage": null,
     *      "ee": null,
     *      "timeliness": null,
     *      "project_id": 1,
     *      "created_at": "2019-02-25 03:05:05",
     *      "updated_at": "2019-02-25 03:05:05",
     *      "used_effort": 1.44
     *      "plan_effort": 2.5
     *   },
     *   {
     *      "id": 2,
     *      "title": "abcd",
     *      "status": 1,
     *      "from_at": "2019-02-25",
     *      "to_at": "2019-02-25",
     *      "budget": "20.4",
     *      "note": "2dag0",
     *      "css": null,
     *      "leakage": null,
     *      "ee": null,
     *      "timeliness": null,
     *      "project_id": 1,
     *      "created_at": "2019-02-25 03:05:05",
     *      "updated_at": "2019-02-25 03:05:05",
     *      "used_effort": 2.34
     *      "plan_effort": 2.5
     *   }
     * ]
     * }
     */
    public function main(Request $request)
    {
        $responseData = $this->phaseService->index($request->projectId);
        return response()->json($responseData, 200);
    }
}
