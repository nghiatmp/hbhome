<?php

namespace App\Http\Controllers\API\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\UserService;

/**
 * @group Users
 */
class SearchController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    
    /**
     * Search
     * Search users
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
     *          "id": 1,
     *          "full_name": "Admin",
     *          "email": "admin@hblab.co.jp",
     *          "role": "7"
     *      },
     *      {
     *          "id": 2,
     *          "full_name": "Admin",
     *          "email": "admin@hblab.co.jp",
     *          "role": "15"
     *      }
     *  ],
     *  "keyword": "a",
     *  "sort_type": "desc",
     *  "order_by": "id",
     *  "current_page": 1,
     *  "per_page": 20,
     *  "last_page": 1,
     *  "total": 12
     * }
     */
    public function main(Request $request)
    {
        $this->validation($request);
        $params = $this->getData($request);
        $responseData = $this->userService->search($params);
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
                Rule::in(['id', 'full_name', 'email', 'role']),
            ],
        ]);
    }

    protected function getData(Request $request)
    {
        $params = [
            'keyword' => null,
            'sortType' => 'desc',
            'orderBy' => 'id',
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
