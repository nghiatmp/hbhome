<?php

namespace App\Http\Controllers\API\Phases;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Services\PhaseService;

/**
 * @group Phases
 */
class MySearchController extends Controller
{
    protected $phaseService;

    public function __construct(PhaseService $phaseService)
    {
        $this->phaseService = $phaseService;
    }

    /**
     * Search my phases
     * Search all the phases of current user
     *
     * @queryParam page integer nullable
     * @queryParam keyword string nullable
     * @queryParam sortType string nullable
     * @queryParam orderBy string nullable
     *
     * @return Array
     *
     * @response {
     *  "data": [
     *      {
     *      "id": 2,
     *      "title": "ph2",
     *      "status": 1,
     *      "from_at": "2019-03-05",
     *      "to_at": "2019-06-07",
     *      "note": "no",
     *      "budget": "20.00",
     *      "css": null,
     *      "leakage": null,
     *      "ee": null,
     *      "timeliness": null,
     *      "project_id": 1,
     *      "project_name": "ABC",
     *      "used_effort": 0.04,
     *      "plan_effort": 0.04
     *      }
     *  ]
     *  "keyword": "ph",
     *  "sort_type": "desc",
     *  "order_by": "id",
     *  "current_page": 1,
     *  "per_page": 20,
     *  "last_page": 1,
     *  "total": 1
     * }
     *
     * @response 403 {
     *   "message": "Forbidden",
     *   "errors": {}
     * }
     */
    public function main(Request $request)
    {
        $this->validation($request);
        $params = $this->getData($request);
        $responseData = $this->phaseService->mySearch($params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'keyword' => 'nullable|string|between:1,127',
            'sortType' => [
                'nullable',
                'string',
                Rule::in(['asc', 'desc']),
            ],
            'orderBy' => [
                'nullable',
                'string',
                Rule::in(['id', 'title', 'project_name', 'from_at', 'to_at', 'budget', 'status']),
            ],
        ]);
    }

    protected function getData(Request $request)
    {
        $params = [
            'keyword' => null,
            'sortType' => 'desc',
            'orderBy' => 'status',
        ];
        if ($request->has('keyword')) {
            $params['keyword'] = $request->keyword;
        }
        if ($request->has('sortType')) {
            $params['sortType'] = $request->sortType;
        }
        if ($request->has('orderBy')) {
            $params['orderBy'] = $request->orderBy;
        }
        return $params;
    }
}
