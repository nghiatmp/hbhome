<?php

namespace App\Http\Controllers\API\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProjectUserService;

/**
 * @group Projects
 */
class IndexMemberController extends Controller
{
    protected $projectUserService;

    public function __construct(ProjectUserService $projectUserService)
    {
        $this->projectUserService = $projectUserService;
    }
    
    /**
     * Index member
     * Get all members in project
     *
     * @return Array
     *
     * @response {
     * "data": [
     *      {
     *          "id": 1,
     *          "user_id": 1,
     *          "role": "1",
     *          "is_member": "1",
     *          "user": {
     *              "id": 1,
     *              "full_name": "ada",
     *              "email": "abc@aojda"
     *          }
     *      },
     *      {
     *          "id": 2,
     *          "user_id": 2,
     *          "role": "1",
     *          "is_member": "0",
     *          "user": {
     *              "id": 2,
     *              "full_name": "adad",
     *              "email": "adada@ahka"
     *          }
     *      }
     *  ]
     * }
     */
    public function main(Request $request)
    {
        $responseData = $this->projectUserService->index($request->projectId);
        return response()->json($responseData, 200);
    }
}
