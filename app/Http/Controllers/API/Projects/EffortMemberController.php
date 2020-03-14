<?php

namespace App\Http\Controllers\API\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\EffortService;

/**
 * @group Projects
 */
class EffortMemberController extends Controller
{
    protected $effortService;

    public function __construct(EffortService $effortService)
    {
        $this->effortService = $effortService;
    }

    /**
     * Get member effort
     * Get effort per member
     *
     * @queryParam from date nullable
     * @queryParam to date nullable
     *
     * @return Array
     *
     * @response {
     *  "from": "2019-01-01",
     *  "to": "2019-11-01",
     *  "users": [
     *      {
     *          "id": 2,
     *          "name": "Admin",
     *          "email": "Admin@ahfa.com",
     *          "effort": 0.18
     *      },
     *      {
     *          "id": 8,
     *          "name": "abc",
     *          "email": "abc@ahfa.com",
     *          "effort": 0.22
     *      }
     *  ]
     * }
     */
    public function main(Request $request)
    {
        $this->validation($request);
        $params = $this->getParams($request);
        $responseData = $this->effortService->getEffortPerMember($request->projectId, $params);
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
