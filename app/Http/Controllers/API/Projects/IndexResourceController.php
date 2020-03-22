<?php

namespace App\Http\Controllers\API\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ResourceService;

/**
 * @group Projects
 */
class IndexResourceController extends Controller
{
    protected $resourceService;

    public function __construct(ResourceService $resourceService)
    {
        $this->resourceService = $resourceService;
    }

    /**
     * Index resource
     * Get all resources in project
     *
     * @return Array
     *
     * @response {
     * "data": [
     *      {
     *          "id": 1,
     *          "user_id": 1,
     *          "role": "1",
     *          "from_at": "2019-02-25",
     *          "to_at": "2019-02-25",
     *          "allocation": "20",
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
     *          "from_at": "2019-02-25",
     *          "to_at": "2019-02-25",
     *          "allocation": "20",
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
        $this->validation($request);
        $params = $this->getParams($request);
        $responseData = $this->resourceService->index($request->projectId, $params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);
    }

    protected function getParams(Request $request)
    {
        $params['from'] = null;
        $params['to'] = null;
        if ($request->has(['from', 'to'])) {
            $params['from'] = $request->from;
            $params['to'] = $request->to;
        }
        return $params;
    }
}
