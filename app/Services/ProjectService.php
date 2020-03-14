<?php

namespace App\Services;

use App\Enums\ActivityLogTypes;
use App\Helpers\ActivityLogHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Enums\ProjectMemberStatus;
use App\Enums\ProjectRole;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Enums\HideSettingType;
use App\Enums\ProjectStatus;

class ProjectService
{
    private $project;
    private $projectUser;
    private $activityLogHelper;
    private $hideSettingService;
    private $activityLogService;

    public function __construct(
        Project $project,
        ProjectUser $projectUser,
        HideSettingService $hideSettingService,
        ActivityLogHelper $activityLogHelper,
        ActivityLogService $activityLogService
    ) {
        $this->project = $project;
        $this->projectUser = $projectUser;
        $this->activityLogHelper = $activityLogHelper;
        $this->hideSettingService = $hideSettingService;
        $this->activityLogService = $activityLogService;
    }

    /**
     * Find Project by id
     * @param integer $id
     * @return Project
     */
    public function find($id)
    {
        return $this->project->find($id);
    }

    /**
     * Index project
     *
     * @return array
     */
    public function index()
    {
        $projects = $this->project->with('phases', 'team')->get()->toArray();

        return [
            'data' => $projects
        ];
    }

    /**
     * @param ['title', 'key', 'contract', 'rank', 'note', 'team_id', 'status', 'backlog_key', 'tms_key',
     * 'admin_id', 'role', 'is_member'] $params
     *
     * @return mixed
     */
    public function store($params)
    {
        return DB::transaction(function () use ($params) {
            $projectObject = $this->project->create([
                'title' => $params['title'],
                'key' => $params['key'],
                'contract' => $params['contract'],
                'rank' => $params['rank'],
                'note' => $params['note'],
                'team_id' => $params['team_id'],
                'status' => $params['status'],
                'backlog_key' => $params['backlog_key'],
                'tms_key' => $params['tms_key'],
            ]);
            if (array_key_exists('admin_id', $params)) {
                $projectObject->users()->attach($params['admin_id'], [
                    'role' => $params['role'],
                    'is_member' => $params['is_member'],
                ]);
            }
            //add action activity log
            $this->activityLogService
                ->store($projectObject->id, ActivityLogTypes::PROJECT, config('constant.ACTION_LOG_CONTENT.INSERT'));
            return $projectObject;
        });
    }

    /**
     * @param integer $id
     * @param ['title', 'key', 'contract', 'rank', 'note', 'team_id'] $params
     *
     * @return mixed
     */
    public function update($id, $params)
    {
        return DB::transaction(function () use ($id, $params) {
            $projectColumn = $this->getProjectColumns();
            unset($projectColumn['project.id']);
            $projectObject = $this->project->find($id);
            $oldProject = $this->activityLogHelper->getObjectArrayByFields($projectColumn, $projectObject);
            $projectObject->update($params);
            $newProject = $this->activityLogHelper->getObjectArrayByFields($projectColumn, $projectObject);
            $contentUpdate = $this->activityLogHelper->createContentUpdate($oldProject, $newProject);
            $contentUpdate = config('constant.ACTION_LOG_CONTENT.UPDATE'). ' <br />'. $contentUpdate;
            $this->activityLogService->store($id, ActivityLogTypes::PROJECT, $contentUpdate);
            return $projectObject;
        });
    }


    /**
    /**
     * @param integer $id
     *
     */
    public function show($id)
    {
        $projectObject = $this->project->with('team')->find($id);
        $projectObject->admins = $this->projectUser
            ->where('project_id', $id)
            ->where('role', ProjectRole::ADMIN)
            ->where('is_member', ProjectMemberStatus::ACTIVE)
            ->pluck('user_id');

        return $projectObject;
    }

    /**
     * @return array
     *
     */
    public function myIndex()
    {
        $projectHideIds = $this->hideSettingService->getHideSettingIdsByType(HideSettingType::PROJECT);
        $projects = Auth::user()
            ->projects()
            ->where(function ($query) {
                $query->where('project_user.role', ProjectRole::ADMIN)
                    ->orWhere(function ($query) {
                        $query->where('project_user.role', '!=', ProjectRole::ADMIN);
                        $query->where('status', ProjectStatus::OPEN);
                    });
            })
            ->wherePivot('is_member', ProjectMemberStatus::ACTIVE)
            ->whereNotIn('projects.id', $projectHideIds)
            ->with('phases', 'team')
            ->get();

        return [
            'data' => $projects
        ];
    }

    /**
     * @param ['keyword', 'sortType', 'orderBy'] $params
     *
     * @return array
     */
    public function search($params)
    {
        $columns = $this->getProjectColumns();
        $keyword = $params['keyword'];
        $sortType = $params['sortType'];
        $orderBy = $params['orderBy'];
        $projects = $this->project
            ->when(!is_null($keyword), function ($query) use ($keyword) {
                $query->where('title', 'like', '%'.$keyword.'%')
                    ->orWhere('key', 'like', '%'.$keyword.'%');
            })
            ->orderBy($orderBy, $sortType)
            ->with('team')
            ->paginate(20, $columns, 'page')
            ->toArray();

        return [
            'data' => $projects['data'],
            'keyword' => $keyword,
            'sort_type' => $sortType,
            'order_by' => $orderBy,
            'current_page' => $projects['current_page'],
            'per_page' => $projects['per_page'],
            'last_page' => $projects['last_page'],
            'total' => $projects['total'],
        ];
    }

    /**
     * @param ['keyword', 'sortType', 'orderBy'] $params
     *
     */
    public function mySearch($params)
    {
        $projectHideIds = $this->hideSettingService->getHideSettingIdsByType(HideSettingType::PROJECT);
        $columns = $this->getProjectColumns();
        $keyword = $params['keyword'];
        $sortType = $params['sortType'];
        $orderBy = $params['orderBy'];
        $projects = Auth::user()
            ->projects()
            ->when(!is_null($keyword), function ($query) use ($keyword) {
                $query->where(function ($queryWhere) use ($keyword) {
                    $queryWhere->where('title', 'like', '%'.$keyword.'%')
                        ->orWhere('key', 'like', '%'.$keyword.'%');
                });
            })
            ->where(function ($query) {
                $query->where('project_user.role', ProjectRole::ADMIN)
                    ->orWhere(function ($query) {
                        $query->where('project_user.role', '!=', ProjectRole::ADMIN);
                              $query->where('status', ProjectStatus::OPEN);
                    });
            })
            ->whereNotIn('projects.id', $projectHideIds)
            ->where('is_member', ProjectMemberStatus::ACTIVE)
            ->orderBy($orderBy, $sortType)
            ->with('team')
            ->paginate(20, $columns, 'page')
            ->toArray();

        return [
            'data' => $projects['data'],
            'keyword' => $keyword,
            'sort_type' => $sortType,
            'order_by' => $orderBy,
            'current_page' => $projects['current_page'],
            'per_page' => $projects['per_page'],
            'last_page' => $projects['last_page'],
            'total' => $projects['total'],
        ];
    }

    protected function getProjectColumns()
    {
        return [
            'projects.id',
            'title',
            'key',
            'note',
            'from_at',
            'to_at',
            'contract',
            'rank',
            'budget',
            'team_id',
            'status',
            'css',
            'leakage',
            'ee',
            'timeliness',
            'backlog_key',
            'tms_key'
        ];
    }

    /**
     * @param ['key'] $params
     *
     */
    public function keyExists($params)
    {
        $key = $params['key'];
        return [
            'has_key' => $this->project->where('key', $key)->exists(),
        ];
    }


    /**
     * @return array
     */
    public function all()
    {
        $projects = $this->project->orderBy('to_at', 'desc')->get(['id', 'title', 'to_at'])->toArray();
        return [
            'data' => $projects,
        ];
    }

    /**
     * @return array
     */
    public function myAll()
    {
        $projects = Auth::user()
            ->projects()
            ->wherePivot('is_member', ProjectMemberStatus::ACTIVE)
            ->orderBy('to_at', 'desc')
            ->get(['project_id as id', 'title', 'to_at'])
            ->toArray();
        return [
            'data' => $projects,
        ];
    }
}
