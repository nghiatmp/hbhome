<?php

namespace App\Http\Controllers\API\Phases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use BenSampo\Enum\Rules\EnumValue;
use App\Services\PhaseService;
use App\Enums\PhaseStatus;

/**
 * @group Phases
 */
class ChangeStatusController extends Controller
{
    protected $phaseService;

    public function __construct(PhaseService $phaseService)
    {
        $this->phaseService = $phaseService;
    }
    
    /**
     * Change status
     * Change status and update phase with given input
     *
     * @bodyParam phaseId integer required
     * @bodyParam status integer required
     * @bodyParam css decimal required
     * @bodyParam leakage decimal required
     * @bodyParam ee decimal required
     * @bodyParam timeliness decimal required
     *
     * @return Object
     *
     * @response {
     *   "id": 1,
     *   "title": "abc",
     *   "status": 1,
     *   "from_at": "2019-02-25",
     *   "to_at": "2019-02-25",
     *   "budget": "20.4",
     *   "note": "2dag0",
     *   "css": 90,
     *   "leakage": 90,
     *   "ee": 18,
     *   "timeliness": 90,
     *   "project_id": 1,
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
        $responseData = $this->phaseService->changeStatus($request->phaseId, $params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'status' => ['required', new EnumValue(PhaseStatus::class, false)],
            'css' => 'required|numeric|between:0,100',
            'leakage' => 'required|numeric|gte:0',
            'ee' => 'required|numeric|gte:0',
            'timeliness' => 'required|numeric|between:0,100',
        ]);
    }

    protected function getData(Request $request)
    {
        return $request->only(['status', 'css', 'leakage', 'ee', 'timeliness']);
    }
}
