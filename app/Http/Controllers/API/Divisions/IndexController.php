<?php

namespace App\Http\Controllers\API\Divisions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DivisionService;

/**
 * @group Divisions
 */
class IndexController extends Controller
{
    protected $divisionService;

    public function __construct(DivisionService $divisionService)
    {
        $this->divisionService = $divisionService;
    }
    
    /**
     * Index
     * Get list divisions
     *
     * @return Array Objects
     *
     * @response 200 {
     *  "data": [
     *      {
     *          "id": 1,
     *          "title": "D1"
     *      },
     *      {
     *          "id": 2,
     *          "title": "D2"
     *      }
     *  ]
     * }
     */
    public function main(Request $request)
    {
        $responseData = $this->divisionService->index();
        return response()->json($responseData, 200);
    }
}
