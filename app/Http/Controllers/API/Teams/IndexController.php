<?php

namespace App\Http\Controllers\API\Teams;

use App\Enums\HideSettingType;
use App\Http\Controllers\Controller;
use App\Models\UserTeam;
use App\Services\HideSettingService;
use App\Services\UserTeamService;
use Illuminate\Http\Request;
use App\Services\TeamService;

/**
 * @group Teams
 */
class IndexController extends Controller
{
    protected $teamService;
    protected $hideSettingService;

    public function __construct(TeamService $teamService, HideSettingService $hideSettingService)
    {
        $this->teamService = $teamService;
        $this->hideSettingService = $hideSettingService;
    }
    
    /**
     * Index
     * Get list Teams
     *
     * @return Array Objects
     *
     * @response {
     *  "data": [
     *      {
     *          "id": 1,
     *          "title": "abc",
     *          "childrens": [{
     *              "id": 3,
     *              "title": "abc",
     *              "childrens": []
     *              }
     *           ]
     *      },
     *      {
     *          "id": 2,
     *          "title": "abcd",
     *          "childrens": [{
     *              "id": 3,
     *              "title": "abc",
     *              "childrens": []
     *              }
     *           ]
     *      }
     *  ]
     * }
     */
    public function main(Request $request)
    {
        $params = $request->all();
        if (isset($params['all']) && $params['all']) {
            $idsHide = [];
        } else {
            $idsHide = $this->hideSettingService->getHideSettingIdsByType(HideSettingType::GROUP);
        }
        $responseData = $this->teamService->index($idsHide);
        $dataTreeView = $this->teamService->getDataTeamForTreeView($responseData['data']);
        $dataReturn['data'] = $dataTreeView;
        return response()->json($dataReturn, 200);
    }
}
