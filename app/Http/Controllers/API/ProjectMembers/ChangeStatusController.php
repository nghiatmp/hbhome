<?php

namespace App\Http\Controllers\API\ProjectMembers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use BenSampo\Enum\Rules\EnumValue;
use App\Enums\ProjectMemberStatus;
use App\Services\ProjectUserService;

/**
 * @group Project Members
 */
class ChangeStatusController extends Controller
{
    protected $projectUserService;

    public function __construct(ProjectUserService $projectUserService)
    {
        $this->projectUserService = $projectUserService;
    }
    
    /**
     * Change status
     * Change member status
     *
     * @bodyParam projectMemberId integer required
     * @bodyParam is_member boolean required
     *
     * @return Object
     *
     * @response {
     *   "id": "1",
     *   "project_id": "1",
     *   "user_id": "1",
     *   "role": "7",
     *   "is_member": "1",
     *   "created_at": "2019-02-22 01:52:39",
     *   "updated_at": "2019-02-22 01:52:39"
     * }
     *
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {}
     * }
     *
     * @response 404 {
     *   "message": "Not Found",
     *   "errors": {}
     * }
     */
    public function main(Request $request)
    {
        $this->validation($request);
        $params = $this->getData($request);
        $responseData = $this->projectUserService->update($request->projectMemberId, $params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'is_member' => ['required', new EnumValue(ProjectMemberStatus::class, false)],
        ]);
    }

    protected function getData(Request $request)
    {
        return $request->only(['is_member']);
    }
}
