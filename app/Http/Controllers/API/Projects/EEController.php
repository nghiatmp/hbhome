<?php

namespace App\Http\Controllers\API\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\EffortService;

/**
 * @group Projects
 */
class EEController extends Controller
{
    protected $effortService;

    public function __construct(EffortService $effortService)
    {
        $this->effortService = $effortService;
    }

    /**
     * Get EE
     * Get EE
     *
     * @return Array
     *
     * @response 200 {
     *  "budget": 20.00,
     *  "effort": 12.99,
     *  "ee": 80.20
     * }
     */
    public function main(Request $request)
    {
        $responseData = $this->effortService->getEE($request->projectId);
        return response()->json($responseData, 200);
    }
}
