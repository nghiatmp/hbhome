<?php

namespace App\Http\Controllers\API\HideSetting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use BenSampo\Enum\Rules\EnumValue;
use App\Enums\HideSettingType;
use App\Services\HideSettingService;

class StoreController extends Controller
{
    protected $hideSettingService;

    public function __construct(HideSettingService $hideSettingService)
    {
        $this->hideSettingService = $hideSettingService;
    }

    /**
     * Store
     * Add new hide setting
     *
     * @bodyParam type string required
     * @bodyParam ids string required
     * @bodyParam created_by integer required
     *
     * @return Object
     *
     * @response {
     *   "id": 1,
     *   "type": 1,
     *   "ids": "1,2",
     *   "created_by": 1,
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
        $responseData = $this->hideSettingService->store($params);
        return response()->json($responseData, 200);
    }

    protected function validation(Request $request)
    {
        return $request->validate([
            'type' => ['required', new EnumValue(HideSettingType::class, false)],
            'created_by' =>'required|integer|exists:users,id',

        ]);
    }

    protected function getData(Request $request)
    {
        $param = $request->only(['type', 'ids', 'created_by']);
        if ($param['ids'] == null) {
            $param['ids'] = '';
        }
        return $param;
    }
}
