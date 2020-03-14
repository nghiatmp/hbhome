<?php

namespace App\Http\Controllers\API\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use BenSampo\Enum\Rules\EnumValue;
use App\Enums\ProjectRole;
use App\Services\ResourceService;

/**
 * @group Projects
 */
class StoreResourceController extends Controller
{
    protected $resourceService;

    public function __construct(ResourceService $resourceService)
    {
        $this->resourceService = $resourceService;
    }
    
    /**
     * Store resource
     * Add new resource with given input
     *
     * @bodyParam projectId integer required
     * @bodyParam user_id integer required
     * @bodyParam role integer required
     * @bodyParam from_at date required
     * @bodyParam to_at date required
     * @bodyParam allocation integer required
     *
     * @return Object
     *
     * @response {
     *   "project_id": 29,
     *   "user_id": 1,
     *   "role": "1",
     *   "from_at": "2019-02-25",
     *   "to_at": "2019-02-25",
     *   "allocation": "20",
     *   "created_at": "2019-02-25 09:26:57",
     *   "updated_at": "2019-02-25 09:26:57"
     * }
     *
     * @response 400 {
     *   "message": "Bad Request",
     *   "errors": {}
     * }
     *
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {}
     * }
     */
    public function main(Request $request)
    {
        $this->validation($request);
        $params = $this->getData($request);
        $responseData = $this->resourceService->store($params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'user_id' => [
                'required',
                'integer',
                Rule::exists('project_user', 'user_id')
                    ->where(function ($query) use ($request) {
                        $query->where('project_id', $request->projectId);
                    })
            ],
            'from_at' => 'required|date',
            'to_at' => 'required|date|after_or_equal:from_at',
            'allocation' => 'required|integer|between:0,100',
            'role' => ['required', new EnumValue(ProjectRole::class, false)],
        ]);
    }

    protected function getData(Request $request)
    {
        $params = $request->only(['user_id', 'role', 'from_at', 'to_at', 'allocation']);
        $params['project_id'] = $request->projectId;
        return $params;
    }
}
