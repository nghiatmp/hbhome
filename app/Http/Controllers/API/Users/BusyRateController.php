<?php

namespace App\Http\Controllers\API\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BusyRateService;

/**
 * @group Users
 */
class BusyRateController extends Controller
{
    protected $busyRateService;

    public function __construct(BusyRateService $busyRateService)
    {
        $this->busyRateService = $busyRateService;
    }
    
    /**
     * Busy rate
     * Get user's busy rate per month
     *
     * @bodyParam userId integer required
     *
     * @return Object
     *
     * @response {
     *  "from": "2019-01-01",
     *  "to": "2019-07-31",
     *  "busyRates": {
     *      "2019-01": 17,
     *      "2019-02": 43,
     *      "2019-03": 32,
     *      "2019-04": 88,
     *      "2019-05": 89,
     *      "2019-06": 90,
     *      "2019-07": 36
     *  }
     * }
     */
    public function main(Request $request)
    {
        $this->validation($request);
        $params = $this->getParams($request);
        $responseData = $this->busyRateService->getUserBusyRate($request->userId, $params);
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
