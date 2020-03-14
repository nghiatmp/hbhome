<?php

namespace App\Http\Controllers\API\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\ProjectService;

/**
 * @group Projects
 */
class SearchController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Search
     * Search projects
     *
     * @queryParam page integer nullable
     * @queryParam keyword string nullable
     * @queryParam sortType string nullable
     * @queryParam orderBy string nullable
     *
     * @return Array
     *
     * @response 200 {
     *  "data": [
     *      {
     *          "id": 29,
     *          "title": "abc",
     *          "key": "ladfla",
     *          "note": "olahfal",
     *          "from_at": "2019-02-25 09:26:57",
     *          "to_at": "2019-02-25 09:26:57",
     *          "contract": "1",
     *          "rank": "2",
     *          "team_id": "3",
     *          "status": 1
     *      },
     *      {
     *          "id": 2,
     *          "title": "absc",
     *          "key": "ladflasa",
     *          "note": "olahfal",
     *          "from_at": "2019-02-25 09:26:57",
     *          "to_at": "2019-02-25 09:26:57",
     *          "contract": "1",
     *          "rank": "2",
     *          "team_id": "3",
     *          "status": 1
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
     */
    public function main(Request $request)
    {
        $this->validation($request);
        $params = $this->getParams($request);
        $responseData = $this->projectService->search($params);
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
                Rule::in(['id', 'title', 'key', 'from_at', 'to_at', 'contract', 'rank', 'team_id', 'status']),
            ],
            'keyword' => 'nullable|string|max:127',
        ]);
    }

    protected function getParams(Request $request)
    {
        $params = [
            'sortType' => 'desc',
            'orderBy' => 'id',
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
