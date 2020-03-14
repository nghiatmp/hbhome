<?php

namespace App\Http\Controllers\API\HideSetting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\HideSettingService;

class IndexController extends Controller
{
    protected $hideSettingService;

    public function __construct(HideSettingService $hideSettingService)
    {
        $this->hideSettingService = $hideSettingService;
    }
    
    /**
     * Index
     * Get list hideSetting
     *
     * @return Array Objects
     *
     * @response 200 {
     *  "data": [
     *      {
     *          "id": 1,
     *          "type": 1,
     *          "ids": "1,2",
     *          "created_by": 1,
     *      },
     *      {
     *          "id": 2,
     *          "type": 1,
     *          "ids": "1,2",
     *          "created_by": 2,
     *      }
     *  ]
     * }
     */
    public function main(Request $request)
    {
        $responseData = $this->hideSettingService->index();
        return response()->json($responseData, 200);
    }
}
