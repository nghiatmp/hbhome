<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\EffortService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\ResourceService;

/**
 * @group Projects
 */
class ResourceTotalController extends Controller
{
    protected $resourceService;
    protected $effortService;

    public function __construct(ResourceService $resourceService, EffortService $effortService)
    {
        $this->resourceService = $resourceService;
        $this->effortService = $effortService;
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
        $baseData = $this->createBaseDataMessage($params['from'],Carbon::parse($params['to'])->lastOfMonth()->format('d-m-Y'),'m-Y');
        $responseDatas = $this->effortService->getEffortPerMonthAllProject($params);
        $res = [];
        foreach ( $responseDatas as $key => $responseData) {
            $dataReturn = $baseData;
            $dataReturn['projects'] = $key;
            foreach ($responseData as $key => $val){
                $dataReturn[$key] = $val;
            }
            $res[] = $dataReturn;
        }
        return response()->json($res, 200);
    }
    public function createBaseDataMessage($startAt, $endAt, $format)
    {
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod(new \DateTime($startAt), $interval, new \DateTime($endAt));

        $baseData = [];
        $baseData['projects'] = '';
        foreach ($period as $date) {
            $baseData[$date->format($format)] = 0;
        }
        return $baseData;
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
