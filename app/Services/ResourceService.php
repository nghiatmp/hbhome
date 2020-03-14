<?php

namespace App\Services;

use App\Enums\ActivityLogTypes;
use App\Enums\ProjectRole;
use App\Helpers\ActivityLogHelper;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\Resource;

class ResourceService
{
    private $project;
    private $resource;
    private $activityLogService;
    private $activityLogHelper;
    private $userService;

    public function __construct(
        Resource $resource,
        Project $project,
        ActivityLogService $activityLogService,
        ActivityLogHelper $activityLogHelper,
        UserService $userService
    ) {
        $this->project = $project;
        $this->resource = $resource;
        $this->activityLogService = $activityLogService;
        $this->activityLogHelper = $activityLogHelper;
        $this->userService = $userService;
    }

    /**
     * @param integer $id
     */
    public function find($id)
    {
        return $this->resource->find($id);
    }

    /**
     * @param ['project_id', 'user_id', 'role', 'from_at', 'to_at', 'allocation'] $params
     *
     */
    public function store($params)
    {
        $this->checkValidTimeRange($params['project_id'], $params['from_at'], $params['to_at']);
        return DB::transaction(function () use ($params) {
            $resourceObject = $this->resource->create($params);
            //get resource detail
            $resourceDetail = User::findOrFail($resourceObject->user_id);
            //add action activity log
            $this->activityLogService->store(
                $params['project_id'],
                ActivityLogTypes::RESOURCE,
                config('constant.ACTION_LOG_CONTENT.INSERT'). ' resource '. $resourceDetail->email
            );
            $this->checkLimitAllocation($params['user_id'], $params['from_at'], $params['to_at']);
            return $resourceObject;
        });
    }

    /**
     * @param integer $projectId
     *
     */
    public function index($projectId)
    {
        $resources = $this->resource
            ->with('user:id,full_name,email')
            ->where('project_id', $projectId)
            ->get(['id', 'user_id', 'role', 'from_at', 'to_at', 'allocation'])
            ->toArray();

        return [
            'data' => $resources
        ];
    }

    /**
     * @param integer $userId
     * @param ['from', 'to'] $params
     *
     */
    public function indexByUser($userId, $params)
    {
        $columns = ['id', 'project_id', 'user_id', 'role', 'from_at', 'to_at', 'allocation'];
        $resources = $this->resource
            ->with('user:id,full_name,email')
            ->with('project:id,title')
            ->where('user_id', $userId)
            ->when(!is_null($params['from']) && !is_null($params['to']), function ($query) use ($params) {
                $query->where('from_at', '>=', $params['from']);
                $query->where('to_at', '<=', $params['to']);
            })
            ->orderBy('from_at', 'desc')
            ->paginate(20, $columns, 'page')
            ->toArray();

        return [
            'data' => $resources['data'],
            'total' => $resources['total'],
            'current_page' => $resources['current_page'],
            'per_page' => $resources['per_page'],
            'last_page' => $resources['last_page'],
        ];
    }

    /**
     * @param $id
     * @param $params
     * @return mixed
     */
    public function update($id, $params)
    {
        $resourceObject = $this->resource->find($id);
        $this->checkValidTimeRange($resourceObject->project_id, $params['from_at'], $params['to_at']);
        return DB::transaction(function () use ($resourceObject, $params) {
            $fields = ['role', 'from_at', 'to_at', 'allocation'];
            $oldResource = $this->activityLogHelper->getObjectArrayByFields($fields, $resourceObject);
            $resourceObject->update($params);
            $newResource = $this->activityLogHelper->getObjectArrayByFields($fields, $resourceObject);
            $contentUpdate = $this->activityLogHelper->createContentUpdate($oldResource, $newResource);
            //get resource detail
            $resourceDetail = User::findOrFail($resourceObject->user_id);
            $contentUpdate = config('constant.ACTION_LOG_CONTENT.UPDATE').' resource '. $resourceDetail->email .
                '<br />'. $contentUpdate;
            $this->activityLogService->store($resourceObject->project_id, ActivityLogTypes::RESOURCE, $contentUpdate);
            $this->checkLimitAllocation($params['user_id'], $params['from_at'], $params['to_at']);
            return $resourceObject;
        });
    }

    /**
     * @param integer $id
     *
     */
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $resourceObject = $this->resource->find($id);
            //get resource detail
            $resourceDetail = User::findOrFail($resourceObject->user_id);
            $this->activityLogService->store(
                $resourceObject->project_id,
                ActivityLogTypes::RESOURCE,
                config('constant.ACTION_LOG_CONTENT.DELETE'). ' resource '. $resourceDetail->email
            );
            $this->resource->destroy($id);
        });
    }

    /**
     * @param integer[] $userIds
     * @param Date $from
     * @param Date $to
     *
     */
    public function getResourcesByUsers($userIds, $from, $to)
    {
        return $this->resource
            ->whereIn('user_id', $userIds)
            ->where('from_at', '<=', $to)
            ->where('to_at', '>=', $from)
            ->get();
    }

    /**
     * @param integer $projectId
     * @param Date $from
     * @param Date $to
     *
     */
    protected function checkValidTimeRange($projectId, $from, $to)
    {
        $projectObject = $this->project->findOrFail($projectId);
        if (is_null($projectObject->from_at)) {
            return abort(400, "You must add at least one phase into this project.");
        }
        if (strtotime($from) < strtotime($projectObject->from_at)
            || strtotime($to) > strtotime($projectObject->to_at)
        ) {
            return abort(400, "Resource's range must be in project's range.");
        }

        return true;
    }
    /**
     * @param integer $userId
     * @param Date $from
     * @param Date $to
     */
    protected function checkLimitAllocation($userId, $from, $to)
    {
        $totalAllocation = $this->resource
            ->where('user_id', '=', $userId)
            ->where('to_at', '>=', $from)
            ->where('from_at', '<=', $to)
            ->sum('allocation');
        if ($totalAllocation > 100) {
            return abort(400, "Allocation of user max limit 100%.");
        }
        return true;
    }

    /**
     * @param $from
     * @param $to
     * @return mixed
     */
    public function findByDuration($from, $to)
    {
        return $this->resource
            ->where('from_at', '<=', $to)
            ->where('to_at', '>=', $from)
            ->orderBy('project_id', 'role')
            ->with('user', 'project')
            ->get();
    }

    /**
     * @param $userIDs
     * @param $from
     * @param $to
     * @return mixed
     */
    public function findByUserIdsAndDuration($userIDs, $from, $to)
    {
        return $this->resource
            ->whereIn('user_id', $userIDs)
            ->where('from_at', '<=', $to)
            ->where('to_at', '>=', $from)
            ->with('user')
            ->get()
            ->toArray();
    }
    /**
     * @param $from
     * @param $to
     * @return array
     */
    public function findResourceFreeByDuration($from, $to)
    {
        //User has allocation < 100
        $resourcesHasAllocation = $this->resource
            ->where('from_at', '<=', $to)
            ->where('to_at', '>=', $from)
            ->groupBy('user_id')
            ->pluck('user_id');
        $resourceFree = $this->userService->findNotInUserIDs($resourcesHasAllocation);
        return [
            'free' => $resourceFree
        ];
    }
}
