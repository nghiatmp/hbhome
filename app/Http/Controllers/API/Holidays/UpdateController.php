<?php

namespace App\Http\Controllers\API\Holidays;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\HolidayService;

/**
 * @group Holidays
 */
class UpdateController extends Controller
{
    protected $holidayService;
    
    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }

    /**
     * Update
     * Update a holiday
     *
     * @bodyParam holidayId integer required
     * @bodyParam title string required
     * @bodyParam date date required
     *
     * @return Object
     *
     * @response {
     *   "id": "1",
     *   "title": "Abc",
     *   "from_at": "2019-01-01",
     *   "to_at": "2019-01-02",
     *   "created_at": "2019-02-22 01:52:39",
     *   "updated_at": "2019-02-22 01:52:39"
     * }
     *
     * @response 403 {
     *   "message": "Forbidden",
     *   "errors": {}
     * }
     *
     * @response 404 {
     *   "message": "Not Found",
     *   "errors": {}
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
        $responseData = $this->holidayService->update($request->holidayId, $params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'title' => 'required|string|max:127',
            'from_at' => [
                'required',
                'date',
                Rule::unique('holidays')->ignore($request->holidayId),
            ],
            'to_at' => [
                'required',
                'date',
                Rule::unique('holidays')->ignore($request->holidayId),
                'after_or_equal:from_at'
            ]
        ]);
    }

    protected function getData(Request $request)
    {
        return $request->only(['title', 'from_at', 'to_at']);
    }
}
