<?php

namespace App\Http\Controllers\API\ActivityLogs;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    protected $activityLogService;

    /**
     * IndexController constructor.
     * @param ActivityLogService $activityLogService
     */
    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    /**
     * @param Request $request
     * @param $projectId
     * @return \Illuminate\Http\JsonResponse
     */
    public function main(Request $request)
    {
        $type = $request->get('type') ? $request->get('type') : null;
        $projectId = $request->get('project_id') ? $request->get('project_id') : null;
        $dateFrom = $request->get('date_from') ? $request->get('date_from') : null;
        $dateTo = $request->get('date_to') ? $request->get('date_to') : null;
        $pagination = $request->get('pagination') ? $request->get('pagination') : false;
        $sortType = $request->get('sort_type') ? $request->get('sort_type') : 'asc';
        $orderBy = $request->get('order_by') ? $request->get('order_by') : 'id';
        $activityLogs = $this->activityLogService
            ->getByProjectIdAndType($projectId, $type, $sortType, $orderBy, $dateFrom, $dateTo, $pagination);
        return response()->json($activityLogs, 200);
    }
}
