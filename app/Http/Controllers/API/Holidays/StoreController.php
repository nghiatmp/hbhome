<?php

namespace App\Http\Controllers\API\Holidays;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\HolidayService;

/**
 * @group Holidays
 */
class StoreController extends Controller
{
    protected $holidayService;

    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }

    /**
     * Store
     * Store a new holiday
     *
     * @bodyParam title string required
     * @bodyParam date date required
     *
     * @return Object
     *
     * @response {
     *   "id": 1,
     *   "title": "New Year",
     *   "date": "2019-01-01",
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
        $responseData = $this->holidayService->store($params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'title' => 'required|string|max:127',
            'from_at' => 'required|date|unique:holidays,from_at',
            'to_at' => 'required|date|unique:holidays,to_at|after_or_equal:from_at',
        ]);
    }

    protected function getData(Request $request)
    {
        return $request->only(['title', 'from_at', 'to_at']);
    }
}
