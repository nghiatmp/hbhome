<?php

namespace App\Http\Controllers\API\Phases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\PhaseService;

/**
 * @group Phases
 */
class SearchController extends Controller
{
    protected $phaseService;

    public function __construct(PhaseService $phaseService)
    {
        $this->phaseService = $phaseService;
    }
    
    /**
     * Search
     * Search phases
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
     *          "id": 21,
     *          "title": "abc",
     *          "project_name": "ladfla",
     *          "from_at": "2019-02-25 09:26:57",
     *          "to_at": "2019-02-25 09:26:57",
     *          "status": 1,
     *          "note": "olahfal",
     *          "budget": "3.00",
     *          "css": "33.77",
     *          "leakage": "18.54",
     *          "ee": "77.77",
     *          "timeliness": "44.33",
     *          "project_id": 1
     *          "used_effort": 2
     *          "plan_effort": 2.5
     *      },
     *      {
     *          "id": 29,
     *          "title": "abcd",
     *          "project_name": "ladflaa",
     *          "from_at": "2019-02-25 09:26:57",
     *          "to_at": "2019-02-25 09:26:57",
     *          "status": 1,
     *          "note": "olahfal",
     *          "budget": "3.00",
     *          "css": "33.77",
     *          "leakage": "18.54",
     *          "ee": "77.77",
     *          "timeliness": "44.33",
     *          "project_id": 2
     *          "used_effort": 2
     *          "plan_effort": 2.5
     *      }
     *  ],
     *  "keyword": "a",
     *  "sort_type": "desc",
     *  "order_by": "id",
     *  "current_page": 1,
     *  "per_page": 20,
     *  "last_page": 1,
     *  "total": 2
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
        $params = $this->getParams($request);
        $responseData = $this->phaseService->search($params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
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
            'keyword' => 'nullable|string|max:127',
        ]);
    }

    protected function getParams(Request $request)
    {
        $params = [
            'sortType' => 'desc',
            'orderBy' => 'status',
            'keyword' => null,
        ];
        if ($request->has('sortType')) {
            $params['sortType'] = $request->sortType;
        }
        if ($request->has('orderBy')) {
            $params['orderBy'] = $request->orderBy;
        }
        if ($request->has('keyword')) {
            $params['keyword'] = $request->keyword;
        }

        return $params;
    }
}
