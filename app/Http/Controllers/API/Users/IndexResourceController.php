<?php

namespace App\Http\Controllers\API\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ResourceService;

/**
 * @group Users
 */
class IndexResourceController extends Controller
{
    protected $resourceService;

    public function __construct(ResourceService $resourceService)
    {
        $this->resourceService = $resourceService;
    }
    
    /**
     * Index resources
     * Get all resources of a user
     *
     * @bodyParam userId integer required
     *
     * @return Array
     *
     * @response {
     *  "data": [
     *      {
     *          "id": 1,
     *          "project_id": 1,
     *          "user_id": 1,
     *          "role": "1",
     *          "from_at": "2019-02-25",
     *          "to_at": "2019-02-25",
     *          "allocation": "20",
     *          "user": {
     *              "id": 1,
     *              "full_name": "ada",
     *              "email": "abc@aojda"
     *          },
     *          "project": {
     *              "id": 1,
     *              "title": "ada"
     *          }
     *      },
     *      {
     *          "id": 2,
     *          "project_id": 2,
     *          "user_id": 1,
     *          "role": "1",
     *          "from_at": "2019-02-25",
     *          "to_at": "2019-02-25",
     *          "allocation": "20",
     *          "user": {
     *              "id": 2,
     *              "full_name": "adad",
     *              "email": "adada@ahka"
     *          },
     *          "project": {
     *              "id": 2,
     *              "title": "adaa"
     *          }
     *      }
     *  ],
     *  "total": 2,
     *  "current_page": 1,
     *  "per_page": 20,
     *  "last_page": 1
     * }
     *
     * @response 403{
     *   "message": "Forbidden",
     *   "errors": {}
     * }
     *
     * @response 422{}
     */

    public function main(Request $request)
    {
        $this->validation($request);
        $params = $this->getParams($request);
        $responseData = $this->resourceService->indexByUser($request->userId, $params);
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
