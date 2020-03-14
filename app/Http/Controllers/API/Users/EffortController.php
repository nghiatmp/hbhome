<?php

namespace App\Http\Controllers\API\Users;

use Illuminate\Http\Request;
use App\Services\EffortService;

class EffortController
{
    protected $effortService;

    public function __construct(EffortService $effortService)
    {
        $this->effortService = $effortService;
    }

    /**
     * Effort
     * Get user's effort per month
     *
     * @bodyParam userId integer required
     * @queryParam from date nullable
     * @queryParam to date nullable
     *
     * @return Array
     *
     * @response {
     *  "from": "2019-01-01",
     *  "to": "2019-03-11",
     *  "efforts": [
     *      {
     *          "month": "2019-01",
     *          "mm": 0.17
     *      },
     *      {
     *          "month": "2019-02",
     *          "mm": 0.43
     *      }
     *  ]
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
        $this->validation($request);
        $params = $this->getParams($request);
        $responseData = $this->effortService->getEffortPerUser($request->userId, $params);
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
