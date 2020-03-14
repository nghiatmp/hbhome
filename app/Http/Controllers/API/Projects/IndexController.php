<?php

namespace App\Http\Controllers\API\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProjectService;

/**
 * @group Projects
 */
class IndexController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }
    
    /**
     * Index
     * Get list projects
     *
     * @queryParam page integer nullable
     *
     * @return Array Objects
     *
     * @response 200 {
     *  "data": [
     *      {
     *          "id": 29,
     *          "title": "abc",
     *          "from_at": "2019-02-25 09:26:57",
     *          "to_at": "2019-02-25 09:26:57",
     *          "contract": "1",
     *          "rank": "2",
     *          "division_id": "3",
     *          "status": 1
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
        $responseData = $this->projectService->index();
        return response()->json($responseData, 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function all()
    {
        $responseData = $this->projectService->all();
        return response()->json($responseData, 200);
    }
}
