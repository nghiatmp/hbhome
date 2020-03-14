<?php

namespace App\Services;

use App\Enums\ActivityLogTypes;
use App\Helpers\ActivityLogHelper;
use App\Models\ProjectUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProjectUserService
{
    private $projectUser;
    private $activityLogService;
    private $activityLogHelper;

    public function __construct(
        ProjectUser $projectUser,
        ActivityLogHelper $activityLogHelper,
        ActivityLogService $activityLogService
    ) {
        $this->projectUser = $projectUser;
        $this->activityLogService = $activityLogService;
        $this->activityLogHelper = $activityLogHelper;
    }

    /**
     * @param integer $projectId
     * @param integer $userId
     *
     */
    public function find($projectId, $userId)
    {
        return $this->projectUser->where('project_id', $projectId)->where('user_id', $userId)->first();
    }

    /**
     * @param integer $id
     *
     */
    public function findById($id)
    {
        return $this->projectUser->find($id);
    }

    /**
     *
     * @return list members
     */
    public function index($projectId)
    {
        $members = $this->projectUser
            ->with('user:id,full_name,email')
            ->where('project_id', $projectId)
            ->orderBy('is_member', 'desc')
            ->get(['id', 'user_id', 'role', 'is_member'])
            ->toArray();

        return [
            'data' => $members,
        ];
    }

    /**
     * @param ['project_id', 'user_id', 'role', 'is_member'] $params
     *
     * @return
     */
    public function store($params)
    {
        return $this->projectUser->create($params);
    }

    /**
     * @param integer $id
     * @param ['role'] || ['is_member'] $params
     *
     * @return
     */
    public function update($id, $params)
    {
        return DB::transaction(function () use ($id, $params) {
            $fields = ['role', 'is_member'];
            $member = $this->projectUser->find($id);
            $oldMember = $this->activityLogHelper->getObjectArrayByFields($fields, $member);
            $member->update($params);
            $newMember = $this->activityLogHelper->getObjectArrayByFields($fields, $member);
            $contentUpdate = $this->activityLogHelper->createContentUpdate($oldMember, $newMember);
            //get resource detail
            $memberDetail = User::findOrFail($member->user_id);

            $contentUpdate = config('constant.ACTION_LOG_CONTENT.UPDATE').
                ' member '. $memberDetail->email.' <br /> '. $contentUpdate;
            $this->activityLogService->store($member->project_id, ActivityLogTypes::MEMBER, $contentUpdate);
            return $member;
        });
    }
}
