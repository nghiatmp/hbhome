<?php

namespace App\Http\Controllers\API\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProjectService;

/**
 * @group Projects
 */
class ShowController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }
    
    /**
     * Show
     * Get project's detail
     *
     * @return Object
     *
     * @response {
     *  "id": 29,
     *  "title": "abc",
     *  "key": "abc",
     *  "from_at": "2019-02-25 09:26:57",
     *  "to_at": "2019-02-25 09:26:57",
     *  "contract": "1",
     *  "rank": "2",
     *  "note": "dadas",
     *  "division_id": "3",
     *  "status": 1,
     *  "css": null,
     *  "leakage": null,
     *  "ee": null,
     *  "timeliness": null,
     *  "backlog_key": "",
     *  "tms_key": "",
     *  "created_at": "2019-02-26 02:48:00",
     *  "updated_at": "2019-03-22 09:51:21",
     *  "admins": [1, 2, 4]
     * }
     */
    public function main(Request $request)
    {
        $responseData = $this->projectService->show($request->projectId);
        return response()->json($responseData, 200);
    }
}
