<?php

namespace App\Http\Controllers\API\Holidays;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\HolidayService;

/**
 * @group Holidays
 */
class DestroyController extends Controller
{
    protected $holidayService;

    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }

    /**
     * Destroy
     * Destroy holiday
     *
     * @bodyParam holidayId integer required
     *
     * @return null
     *
     * @response 204 {}
     *
     */
    public function main(Request $request)
    {
        $this->holidayService->destroy($request->holidayId);
        return response()->json([], 204);
    }
}
