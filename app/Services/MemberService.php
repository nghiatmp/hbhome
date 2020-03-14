<?php

namespace App\Services;

use App\Enums\ActivityLogTypes;
use App\Helpers\ActivityLogHelper;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Services\ResourceService;
use App\Services\ProjectUserService;

class MemberService
{
    private $resourceService;
    private $projectUserService;
    private $activityLogService;
    private $activityLogHelper;

    public function __construct(
        ResourceService $resourceService,
        ProjectUserService $projectUserService,
        ActivityLogService $activityLogService,
        ActivityLogHelper $activityLogHelper
    ) {
        $this->resourceService = $resourceService;
        $this->projectUserService = $projectUserService;
        $this->activityLogService = $activityLogService;
        $this->activityLogHelper = $activityLogHelper;
    }

    /**
     * @param ['project_id', 'user_id', 'role', 'is_member', 'from_at', 'to_at', 'allocation'] $params
     *
     */
    public function store($params)
    {
        return DB::transaction(function () use ($params) {
            $projectUserParams = [
                'project_id' => $params['project_id'],
                'user_id' => $params['user_id'],
                'role' => $params['role'],
                'is_member' => $params['is_member'],
            ];
            $projectUserObject = $this->projectUserService->store($projectUserParams);
            //get resource detail
            $memberDetail = User::findOrFail($params['user_id']);
            $this->activityLogService->store(
                $params['project_id'],
                ActivityLogTypes::RESOURCE,
                config('constant.ACTION_LOG_CONTENT.INSERT'). ' member '. $memberDetail->email
            );
            if (array_key_exists('allocation', $params)) {
                $resourceParams = [
                    'project_id' => $params['project_id'],
                    'user_id' => $params['user_id'],
                    'role' => $params['role'],
                    'from_at' => $params['from_at'],
                    'to_at' => $params['to_at'],
                    'allocation' => $params['allocation'],
                ];
                $this->resourceService->store($resourceParams);
            }
            return $projectUserObject;
        });
    }
}
