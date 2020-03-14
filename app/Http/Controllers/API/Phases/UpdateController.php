<?php

namespace App\Http\Controllers\API\Phases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PhaseService;
use App\Enums\PhaseStatus;

/**
 * @group Phases
 */
class UpdateController extends Controller
{
    protected $phaseService;

    public function __construct(PhaseService $phaseService)
    {
        $this->phaseService = $phaseService;
    }
    
    /**
     * Update phase
     * Update phase with given input
     *
     * @bodyParam phaseId integer required
     * @bodyParam title string required
     * @bodyParam from_at date required
     * @bodyParam to_at date required
     * @bodyParam budget double required
     * @bodyParam note string required
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
     *   "css": null,
     *   "leakage": null,
     *   "ee": null,
     *   "timeliness": null,
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
        $responseData = $this->phaseService->update($request->phaseId, $params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'title' => 'required|string|max:127',
            'budget' => 'required|numeric|gt:0',
            'from_at' => 'required|date',
            'to_at' => 'required|date|after_or_equal:from_at',
            'note' => 'nullable|string',
            'budget_details' => 'nullable|array',
        ]);
    }

    protected function getData(Request $request)
    {
        $params = $request->only(['title', 'from_at', 'to_at', 'budget', 'note', 'budget_details']);
        if (is_null($params['note'])) {
            $params['note'] = '';
        }
        return $params;
    }
}
