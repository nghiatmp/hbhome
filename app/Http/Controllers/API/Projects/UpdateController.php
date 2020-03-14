<?php

namespace App\Http\Controllers\API\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use BenSampo\Enum\Rules\EnumValue;
use App\Services\ProjectService;
use App\Enums\ProjectContract;
use App\Enums\ProjectRank;

/**
 * @group Projects
 */
class UpdateController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }
    
    /**
     * Update
     * Update project with given input
     *
     * @bodyParam projectId integer required
     * @bodyParam title string required
     * @bodyParam key string required Unique
     * @bodyParam contract integer required
     * @bodyParam rank integer required
     * @bodyParam note string required
     * @bodyParam division_id integer required
     *
     * @return Object
     *
     * @response {
     *   "id": 29,
     *   "title": "abc",
     *   "key": "dada",
     *   "contract": "1",
     *   "rank": "2",
     *   "note": "asfafhaklhnaldhladj",
     *   "division_id": "3",
     *   "status": 1,
     *   "backlog_key": "",
     *   "tms_key": "",
     *   "updated_at": "2019-02-25 09:26:57",
     *   "created_at": "2019-02-26 09:26:57"
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
        $responseData = $this->projectService->update($request->projectId, $params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'title' => 'required|string|between:1,127',
            'key' => [
                'required',
                'string',
                'between:1,127',
                Rule::unique('projects')->ignore($request->projectId),
            ],
            'contract' => ['required', new EnumValue(ProjectContract::class, false)],
            'rank' => ['required', new EnumValue(ProjectRank::class, false)],
            'note' => 'nullable|string',
            'team_id' => 'required|integer|exists:teams,id',
        ]);
    }

    protected function getData(Request $request)
    {
        $params = $request->only(['title', 'key', 'contract', 'rank', 'note', 'team_id']);
        if (is_null($params['note'])) {
            $params['note'] = '';
        }
        return $params;
    }
}
