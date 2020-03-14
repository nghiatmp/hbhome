<?php

namespace App\Http\Controllers\API\Resources;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ResourceService;

/**
 * @group Resources
 */
class DestroyController extends Controller
{
    protected $resourceService;

    public function __construct(ResourceService $resourceService)
    {
        $this->resourceService = $resourceService;
    }

    /**
     * Delete
     * Delete resource
     *
     * @bodyParam resourceId integer required
     *
     * @return Object
     *
     * @response 204 {}
     */
    public function main(Request $request)
    {
        $responseData = $this->resourceService->destroy($request->resourceId);
        return response()->json($responseData, 204);
    }
}
