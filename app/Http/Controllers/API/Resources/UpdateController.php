<?php

namespace App\Http\Controllers\API\Resources;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use BenSampo\Enum\Rules\EnumValue;
use App\Enums\ProjectRole;
use App\Services\ResourceService;

/**
 * @group Resources
 */
class UpdateController extends Controller
{
    protected $resourceService;

    public function __construct(ResourceService $resourceService)
    {
        $this->resourceService = $resourceService;
    }
    
    /**
     * Update resource
     * Update resource with given input
     *
     * @bodyParam resourceId integer required
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
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {}
     * }
     */
    public function main(Request $request)
    {
        $this->validation($request);
        $params = $this->getData($request);
        $responseData = $this->resourceService->update($request->resourceId, $params);
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
            'role' => ['required', new EnumValue(ProjectRole::class, false)],
            'allocation' => 'required|integer|between:0,100',
            'from_at' => 'required|date',
            'to_at' => 'required|date|after_or_equal:from_at',
        ]);
    }

    protected function getData(Request $request)
    {
        return $request->only(['user_id', 'role', 'from_at', 'to_at', 'allocation']);
    }
}
