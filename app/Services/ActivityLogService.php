<?php

namespace App\Services;

use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    protected $activityLog;

    /**
     * ActivityLogService constructor.
     * @param ActivityLog $activityLog
     */
    public function __construct(ActivityLog $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    /**
     * @param $projectId
     * @param $type
     * @param $content
     * @return mixed
     */
    public function store($projectId, $type, $content)
    {
        $newActivityLog = [
            'project_id' => $projectId,
            'type' => $type,
            'content' => $content,
            'created_by' => Auth::user()->id
        ];
        return $this->activityLog->create($newActivityLog);
    }

    /**
     * @param $projectId
     * @param $type
     * @param $sortType
     * @param $orderBy
     * @param null $dateFrom
     * @param null $dateTo
     * @param bool $pagination
     * @return array
     */
    public function getByProjectIdAndType(
        $projectId,
        $type,
        $sortType,
        $orderBy,
        $dateFrom = null,
        $dateTo = null,
        $pagination = false
    ) {
        $dataReturn = [];
        $dataReturn['sort_type'] = $sortType;
        $dataReturn['order_by'] = $orderBy;
        $data =  $this->activityLog
            ->with('user')
            ->where('project_id', $projectId)
            ->when(!is_null($type), function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->when(!is_null($dateFrom), function ($query) use ($dateFrom) {
                $query->where('created_at', '>=', $dateFrom);
            })
            ->when(!is_null($dateTo), function ($query) use ($dateTo) {
                $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
            })
            ->orderBy($orderBy, $sortType);
        if ($pagination) {
            $data = $data->paginate(intval($pagination))
                ->toArray();
            $activityLogs = $data['data'];
            $dataReturn['current_page'] = $data['current_page'];
            $dataReturn['per_page'] = $data['per_page'];
            $dataReturn['last_page'] = $data['last_page'];
            $dataReturn['total'] = $data['total'];
        } else {
            $activityLogs = $data->get()->toArray();
        }
        $dataActivityLogs = [];
        foreach ($activityLogs as $activityLog) {
            array_push($dataActivityLogs, [
               'id' => $activityLog['id'],
                'project_id' => $activityLog['project_id'],
                'type' => $activityLog['type'],
                'content' => $activityLog['content'],
                'created_at' => $activityLog['created_at'],
                'full_name' => isset($activityLog['user']) ? $activityLog['user']['full_name'] : '',
                'email' => isset($activityLog['user']) ? $activityLog['user']['email'] : ''
            ]);
        }
        $dataReturn['data'] = $dataActivityLogs;
        return $dataReturn;
    }
}
