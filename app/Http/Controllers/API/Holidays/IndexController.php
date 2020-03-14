<?php

namespace App\Http\Controllers\API\Holidays;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\HolidayService;

/**
 * @group Holidays
 */
class IndexController extends Controller
{
    protected $holidayService;

    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }
    
    /**
     * Index
     * Get list holidays
     *
     * @return Array
     *
     * @response {
     *  "data": [
     *      {
     *          "id": 1,
     *          "title": "New Year",
     *          "date": "2019-01-01"
     *      },
     *      {
     *          "id": 2,
     *          "title": "National Day",
     *          "date": "2019-09-02"
     *      }
     *  ],
     *  "current_page": 1,
     *  "per_page": 20,
     *  "last_page": 1,
     *  "total": 20

     * }
     */
    public function main(Request $request)
    {
        $fromAt = $request->get('from_at') ? $request->get('from_at') : null;
        $toAt = $request->get('to_at') ? $request->get('to_at') : null;
        $responseData = $this->holidayService->index($fromAt, $toAt);
        return response()->json($responseData, 200);
    }
}
