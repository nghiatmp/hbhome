<?php

namespace App\Http\Controllers\API\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use BenSampo\Enum\Rules\EnumValue;
use App\Services\ProjectService;
use App\Enums\ProjectContract;
use App\Enums\ProjectMemberStatus;
use App\Enums\ProjectRank;
use App\Enums\ProjectRole;
use App\Enums\ProjectStatus;

/**
 * @group Projects
 */
class StoreController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }
    
    /**
     * Store
     * Create new project with given input
     *
     * @bodyParam title string required
     * @bodyParam key string required Unique
     * @bodyParam contract integer required
     * @bodyParam rank integer required
     * @bodyParam note string required
     * @bodyParam division_id integer required
     * @bodyParam admin_id integer nullable
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
     *   "created_at": "2019-02-25 09:26:57"
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
        $responseData = $this->projectService->store($params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'title' => 'required|string|between:1,127',
            'key' => 'required|string|between:1,127|unique:projects,key',
            'contract' => ['required', new EnumValue(ProjectContract::class, false)],
            'rank' => ['required', new EnumValue(ProjectRank::class, false)],
            'note' => 'nullable|string',
            'team_id' => 'required|integer|exists:teams,id',
            'admin_id' => 'nullable|integer|exists:users,id',
        ]);
    }

    protected function getData(Request $request)
    {
        $params = $request->only(['title', 'key', 'contract', 'rank', 'note', 'team_id']);
        $params['status'] = ProjectStatus::OPEN;
        $params['backlog_key'] = '';
        $params['tms_key'] = '';
        if ($request->has('admin_id')) {
            $params['admin_id'] = $request->only('admin_id');
            $params['role'] = ProjectRole::ADMIN;
            $params['is_member'] = ProjectMemberStatus::ACTIVE;
        }
        if (is_null($params['note'])) {
            $params['note'] = '';
        }
        return $params;
    }
}
