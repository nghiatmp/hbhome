<?php

namespace App\Services;

use App\Enums\ActivityLogTypes;
use App\Enums\PhaseStatus;
use App\Enums\ProjectStatus;
use App\Enums\SystemRole;
use App\Enums\HideSettingType;
use App\Helpers\ActivityLogHelper;
use App\Models\Phase;
use App\Models\Project;
use App\Services\EffortService;
use App\Services\HideSettingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PhaseService
{
    private $phase;
    private $project;
    private $effortService;
    private $hideSettingService;
    private $activityLogService;
    private $activityLogHelper;
    private $budgetDetailService;


    public function __construct(
        Phase $phase,
        Project $project,
        EffortService $effortService,
        HideSettingService $hideSettingService,
        ActivityLogService $activityLogService,
        ActivityLogHelper $activityLogHelper,
        BudgetDetailService $budgetDetailService
    ) {
        $this->phase = $phase;
        $this->project = $project;
        $this->effortService = $effortService;
        $this->hideSettingService = $hideSettingService;
        $this->activityLogService = $activityLogService;
        $this->activityLogHelper = $activityLogHelper;
        $this->budgetDetailService = $budgetDetailService;
    }

    /**
     * @param integer $id
     *
     */
    public function find($id)
    {
        return $this->phase->find($id);
    }

    /**
     * @param integer $id
     * @param ['title', 'from_at', 'to_at', 'budget', 'note'] $params
     *
     */
    public function update($id, $params)
    {
        $phaseObject = $this->phase->find($id);
        $phases = $this->phase
            ->where('project_id', $phaseObject->project_id)
            ->where('id', '<>', $id)
            ->get();
        $from = strtotime($params['from_at']);
        $to = strtotime($params['to_at']);
        if ($this->isDuplicatePhase($phases, $from, $to)) {
            return abort(400, 'Duration of phase is duplicate');
        }
        return DB::transaction(function () use ($phaseObject, $params) {
            $fields = ['title', 'from_at', 'to_at', 'budget', 'note'];
            $budgetDetails = [];
            if (isset($params['budget_details'])) {
                $budgetDetails = $params['budget_details'];
                unset($params['budget_details']);
            }
            $params['budget'] = array_sum($budgetDetails);
            $this->updatePhase($phaseObject, $params, $fields);
            //Add budget detail
            $this->budgetDetailService->updateByPhaseIDAndDetail(
                $phaseObject->id,
                $budgetDetails,
                $phaseObject->from_at,
                $phaseObject->to_at
            );

            return $phaseObject;
        });
    }

    /**
     * @param $phaseObject
     * @param $params
     * @param $fields
     * @return mixed
     */
    public function updatePhase($phaseObject, $params, $fields)
    {
        $oldPhase = $this->activityLogHelper->getObjectArrayByFields($fields, $phaseObject);
        $phaseObject->update($params);
        $newPhase = $this->activityLogHelper->getObjectArrayByFields($fields, $phaseObject);
        $contentUpdate = $this->activityLogHelper->createContentUpdate($oldPhase, $newPhase);
        $contentUpdate = config('constant.ACTION_LOG_CONTENT.UPDATE'). ' phase '. $phaseObject->title
            .'<br />'. $contentUpdate;
        $this->activityLogService->store($phaseObject->project_id, ActivityLogTypes::PHASE, $contentUpdate);
        $this->updateProject($phaseObject->project_id);
        return $phaseObject;
    }

    /**
     * @param ['project_id', 'title', 'status', 'from_at', 'to_at', 'budget', 'note', 'budget_details] $params
     *
     */
    public function store($params)
    {
        $phases = $this->phase->where('project_id', $params['project_id'])->get();
        $from = strtotime($params['from_at']);
        $to = strtotime($params['to_at']);
        if ($this->isDuplicatePhase($phases, $from, $to)) {
            return abort(400, 'Duration of phase is duplicate');
        }
        return DB::transaction(function () use ($params) {
            $budgetDetails = [];
            if (isset($params['budget_details'])) {
                $budgetDetails = $params['budget_details'];
                unset($params['budget_details']);
            }
            $params['budget'] = array_sum($budgetDetails);
            $phaseObject = $this->phase->create($params);

            //Add budget detail
            $this->budgetDetailService->updateByPhaseIDAndDetail(
                $phaseObject->id,
                $budgetDetails,
                $phaseObject->from_at,
                $phaseObject->to_at
            );

            //add action activity log
            $this->activityLogService->store(
                $params['project_id'],
                ActivityLogTypes::PHASE,
                config('constant.ACTION_LOG_CONTENT.INSERT') . ' phase '. $phaseObject->title
            );
            $this->updateProject($params['project_id']);
            return $phaseObject;
        });
    }

    /**
     * @param integer $id
     * @param ['status', 'css', 'leakage', 'ee', 'timeliness'] $params
     *
     */
    public function changeStatus($id, $params)
    {
        $phaseObject = $this->phase->find($id);
        return DB::transaction(function () use ($phaseObject, $params) {
            $fields = ['status', 'css', 'leakage', 'ee', 'timeliness'];
            $this->updatePhase($phaseObject, $params, $fields);
            return $phaseObject;
        });
    }

    /**
     * @param integer $phaseId
     *
     */
    public function destroy($phaseId)
    {
        $phaseObject = $this->phase->find($phaseId);
        return DB::transaction(function () use ($phaseObject) {
            $this->phase->destroy($phaseObject->id);
            if (isset($phaseObject->project_id)) {
                $this->activityLogService->store(
                    $phaseObject->project_id,
                    ActivityLogTypes::PHASE,
                    config('constant.ACTION_LOG_CONTENT.DELETE') . ' phase '. $phaseObject->title
                );
            }
            $this->updateProject($phaseObject->project_id);
        });
    }

    /**
     *
     * @return list phases
     */
    public function index($projectId)
    {
        $phases = $this->phase->where('project_id', $projectId)->get();
        foreach ($phases as $phase) {
            $phase->used_effort = $this->effortService->getEffortPerPhase($phase)['used_effort'];
            $phase->plan_effort = $this->effortService->getEffortPerPhase($phase)['plan_effort'];
            $phase->budget_details = $this->budgetDetailService->findByPhaseID($phase->id);
        }

        return [
            'data' => $phases->toArray(),
        ];
    }

    /**
     * @param integer $id
     *
     */
    public function show($id)
    {
        $phaseObject = $this->phase->find($id);
        $phaseObject->used_effort = $this->effortService->getEffortPerPhase($phaseObject)['used_effort'];
        $phaseObject->plan_effort = $this->effortService->getEffortPerPhase($phaseObject)['plan_effort'];
        return $phaseObject;
    }

    /**
     * @param ['sortType', 'orderBy', 'keyword'] $params
     *
     */
    public function search($params)
    {
        $columns = [
            'phases.id', 'phases.title', 'phases.status',
            'phases.from_at', 'phases.to_at',
            'phases.note', 'phases.budget',
            'phases.css', 'phases.leakage',
            'phases.ee', 'phases.timeliness',
            'phases.project_id',
            'projects.title as project_name',
        ];
        $keyword = $params['keyword'];
        $sortType = $params['sortType'];
        $orderBy = $params['orderBy'];
        $phaseHideIds = $this->hideSettingService->getHideSettingIdsByType(HideSettingType::PHASE);
        $phaseObjects = $this->phase
            ->join('projects', 'project_id', 'projects.id')
            ->when(!is_null($keyword), function ($query) use ($keyword) {
                $query->where('phases.title', 'like', '%' . $keyword . '%')
                    ->orWhere('projects.title', 'like', '%' . $keyword . '%');
            })
            ->when(Auth::user()->role != SystemRole::ADMIN, function ($query) {
                $query->where('phases.status', PhaseStatus::OPEN);
            })
            ->whereNotIn('phases.id', $phaseHideIds)
            ->orderBy($orderBy, $sortType)
            ->paginate(20, $columns, 'page')
            ->toArray();

        foreach ($phaseObjects['data'] as $index => $phaseObject) {
            $planEffort = $this->effortService->getEffortPerPhase((object)$phaseObject)['plan_effort'];
            $usedEffort = $this->effortService->getEffortPerPhase((object)$phaseObject)['used_effort'];
            $phaseObjects['data'][$index]['used_effort'] = $usedEffort;
            $phaseObjects['data'][$index]['plan_effort'] = $planEffort;
        }

        return [
            'data' => $phaseObjects['data'],
            'keyword' => $keyword,
            'sort_type' => $sortType,
            'order_by' => $orderBy,
            'current_page' => $phaseObjects['current_page'],
            'per_page' => $phaseObjects['per_page'],
            'last_page' => $phaseObjects['last_page'],
            'total' => $phaseObjects['total'],
        ];
    }

    /**
     * @param ['sortType', 'orderBy', 'keyword']  $params
     * @return array
     */
    public function mySearch($params)
    {
        $columns = [
            'phases.id', 'phases.title', 'phases.status',
            'phases.from_at', 'phases.to_at',
            'phases.note', 'phases.budget',
            'phases.css', 'phases.leakage',
            'phases.ee', 'phases.timeliness',
            'phases.project_id',
            'projects.title as project_name',
        ];
        $keyword = $params['keyword'];
        $sortType = $params['sortType'];
        $orderBy = $params['orderBy'];
        $me = Auth::user();
        $phaseHideIds = $this->hideSettingService->getHideSettingIdsByType(HideSettingType::PHASE);
        $phaseObjects = $this->phase
            ->join('projects', 'phases.project_id', 'projects.id')
            ->join('project_user', 'phases.project_id', 'project_user.project_id')
            ->where('project_user.user_id', '=', $me->id)
            ->when(!is_null($keyword), function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('phases.title', 'like', '%' . $keyword . '%')
                        ->orwhere('projects.title', 'like', '%' . $keyword . '%');
                });
            })
            ->when(Auth::user()->role != SystemRole::ADMIN, function ($query) {
                $query->where('phases.status', PhaseStatus::OPEN);
            })
            ->whereNotIn('phases.id', $phaseHideIds)
            ->orderBy($orderBy, $sortType)
            ->paginate(20, $columns, 'page')
            ->toArray();

        foreach ($phaseObjects['data'] as $index => $phaseObject) {
            $effort = $this->effortService->getEffortPerPhase((object)$phaseObject);
            $phaseObjects['data'][$index]['used_effort'] = $effort['used_effort'];
            $phaseObjects['data'][$index]['plan_effort'] = $effort['plan_effort'];
        }

        return [
            'data' => $phaseObjects['data'],
            'keyword' => $keyword,
            'sort_type' => $sortType,
            'order_by' => $orderBy,
            'current_page' => $phaseObjects['current_page'],
            'per_page' => $phaseObjects['per_page'],
            'last_page' => $phaseObjects['last_page'],
            'total' => $phaseObjects['total'],
        ];
    }

    /**
     * @param integer $projectId
     *
     */
    protected function updateProject($projectId)
    {
        $projectObject = $this->project->find($projectId);

        $phaseObjects = $projectObject->phases()->orderBy('from_at')->get();
        $phaseNumber = $projectObject->phases()->whereNotNull('css')->count();
        $params = $this->initProjectParams($phaseObjects);

        foreach ($phaseObjects as $phaseObject) {
            $params['budget'] += $phaseObject->budget;
            $params['css'] += $phaseObject->css;
            $params['leakage'] += $phaseObject->leakage;
            $params['ee'] += $phaseObject->ee;
            $params['timeliness'] += $phaseObject->timeliness;
            if ($params['status'] === ProjectStatus::CLOSE && $phaseObject->status === PhaseStatus::OPEN) {
                $params['status'] = ProjectStatus::OPEN;
            }
        }

        if ($phaseNumber === 0) {
            $params['css'] = null;
            $params['leakage'] = null;
            $params['ee'] = null;
            $params['timeliness'] = null;
        } else {
            $params['css'] = round($params['css'] / $phaseNumber, 2);
            $params['leakage'] = round($params['leakage'] / $phaseNumber, 2);
            $params['ee'] = round($params['ee'] / $phaseNumber, 2);
            $params['timeliness'] = round($params['timeliness'] / $phaseNumber, 2);
        }

        $projectObject->update($params);

        return $projectObject;
    }

    protected function isDuplicatePhase($phases, $from, $to)
    {
        foreach ($phases as $phase) {
            $phaseFrom = strtotime($phase->from_at);
            $phaseTo = strtotime($phase->to_at);
            $fromInvalid = $from >= $phaseFrom && $from <= $phaseTo;
            $toInvalid = $to >= $phaseFrom && $to <= $phaseTo;
            $rangeInvalid = $from <= $phaseFrom && $to >= $phaseTo;
            if ($fromInvalid || $toInvalid || $rangeInvalid) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Phase[] $phaseObjects
     *
     */
    protected function initProjectParams($phaseObjects)
    {
        return [
            'status' => ProjectStatus::CLOSE,
            'from_at' => $phaseObjects->first()->from_at,
            'to_at' => $phaseObjects->last()->to_at,
            'budget' => 0,
            'css' => 0,
            'leakage' => 0,
            'ee' => 0,
            'timeliness' => 0,
        ];
    }
}
