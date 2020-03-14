<?php

namespace App\Http\Controllers\API\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\EffortService;

/**
 * @group Projects
 */
class EffortController extends Controller
{
    protected $effortService;

    public function __construct(EffortService $effortService)
    {
        $this->effortService = $effortService;
    }

    /**
     * Get effort
     * Get effort per month
     *
     * @queryParam from date nullable
     * @queryParam to date nullable
     *
     * @return Array
     *
     * @response 200 {
     *  "from": "2019-01-01",
     *  "to": "2019-03-11",
     *  "efforts": [
     *      {
     *          "month": "2019-01",
     *          "mm": 0.17,
     *      },
     *      {
     *          "month": "2019-02",
     *          "mm": 0.43,
     *      }
     *  ]
     * }
     */
    public function main(Request $request)
    {
        $this->validation($request);
        $params = $this->getParams($request);
        $responseData = $this->effortService->getEffortPerMonth($request->projectId, $params);
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
