<?php

namespace App\Http\Controllers\API\Divisions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DivisionService;

/**
 * @group Divisions
 */
class StoreController extends Controller
{
    protected $divisionService;

    public function __construct(DivisionService $divisionService)
    {
        $this->divisionService = $divisionService;
    }

    /**
     * Store
     * Add new division with given input
     *
     * @bodyParam title string required
     *
     * @return Object
     *
     * @response {
     *   "id": "1",
     *   "title": "Abc",
     *   "created_at": "2019-02-22 01:52:39",
     *   "updated_at": "2019-02-22 01:52:39"
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
        $responseData = $this->divisionService->store($params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'title' => 'required|string|max:127|unique:divisions,title',
        ]);
    }

    protected function getData(Request $request)
    {
        return $request->only(['title']);
    }
}
