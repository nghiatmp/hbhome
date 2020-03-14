<?php

namespace App\Http\Controllers\API\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProjectService;

/**
 * @group Projects
 */
class KeyExistsController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }
    
    /**
     * Key exists
     * Check key exists
     *
     * @bodyParam key string required
     *
     * @return Object
     *
     * @response 200 {
     *  "has_key": true
     * }
     */
    public function main(Request $request)
    {
        $this->validation($request);
        $params = $this->getData($request);
        $responseData = $this->projectService->keyExists($params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'key' => 'required|string'
        ]);
    }

    protected function getData(Request $request)
    {
        return $request->only(['key']);
    }
}
