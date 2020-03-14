<?php

namespace App\Http\Controllers\API\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProjectService;

/**
 * @group Projects
 */
class MyIndexController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * My projects
     * Get all my projects
     *
     * @return Array Objects
     *
     * @response 200 {
     *  "data": [
     *      {
     *      "id": 1,
     *      "title": "aabhj",
     *      "from_at": null,
     *      "to_at": null,
     *      "contract": 1,
     *      "rank": 2,
     *      "division_id": 2,
     *      "status": 1
     *      }
     *  ],
     *  "current_page": 1,
     *  "per_page": 20,
     *  "last_page": 1,
     *  "total": 12
     * }
     */
    public function main(Request $request)
    {
        $responseData = $this->projectService->myIndex();
        return response()->json($responseData, 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function all()
    {
        $responseData = $this->projectService->myAll();
        return response()->json($responseData, 200);
    }
}
