<?php

namespace App\Http\Controllers\API\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use BenSampo\Enum\Rules\EnumValue;
use App\Enums\ProjectRole;
use App\Enums\ProjectMemberStatus;
use App\Services\MemberService;

/**
 * @group Projects
 */
class StoreMemberController extends Controller
{
    protected $memberService;

    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }
    
    /**
     * Store member
     * Add new member to project with given input
     *
     * @bodyParam projectId integer required
     * @bodyParam user_id integer required
     * @bodyParam role integer required
     * @bodyParam from_at date nullable
     * @bodyParam to_at date nullable
     * @bodyParam allocation integer nullable
     *
     * @return Object
     *
     * @response {
     *   "id": 1,
     *   "project_id": 29,
     *   "user_id": 1,
     *   "role": "1",
     *   "is_member": "1",
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
        $responseData = $this->memberService->store($params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                'unique:project_user,user_id,NULL,id,project_id,'.$request->projectId,
            ],
            'role' => ['required', new EnumValue(ProjectRole::class, false)],
            'from_at' => 'nullable|date',
            'to_at' => 'nullable|date|after_or_equal:from_at',
            'allocation' => 'nullable|integer|between:0,100',
        ]);
    }

    protected function getData(Request $request)
    {
        if ($request->has(['from_at', 'to_at', 'allocation'])) {
            $params = $request->only(['user_id', 'role', 'from_at', 'to_at', 'allocation']);
        } else {
            $params = $request->only(['user_id', 'role']);
        }
        $params['project_id'] = $request->projectId;
        $params['is_member'] = ProjectMemberStatus::ACTIVE;
        return $params;
    }
}
