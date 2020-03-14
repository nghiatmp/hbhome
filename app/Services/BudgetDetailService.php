<?php

namespace App\Services;

use App\Models\BudgetDetail;
use App\Models\Phase;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class BudgetDetailService
{
    protected $budgetDetail;
    protected $phase;

    /**
     * BudgetDetailService constructor.
     * @param BudgetDetail $budgetDetail
     * @param Phase $phase
     */
    public function __construct(BudgetDetail $budgetDetail, Phase $phase)
    {
        $this->budgetDetail = $budgetDetail;
        $this->phase = $phase;
    }

    /**
     * @param $phaseID
     * @param array $detail
     * @param $from
     * @param $to
     * @return mixed
     */
    public function updateByPhaseIDAndDetail($phaseID, array $detail, $from, $to)
    {
        if (count($detail) == 0) {
            return true;
        }
        $from = strtotime(Carbon::parse($from)->startOfMonth());
        $to = strtotime(Carbon::parse($to)->endOfMonth());
        foreach ($detail as $month => $item) {
            $monthCheck = strtotime(Carbon::parse('01-'.$month)->startOfMonth());
            if ($monthCheck < $from || $monthCheck > $to) {
                return abort(400, trans('http.400'));
            }
        }
        return DB::transaction(function () use ($phaseID, $detail) {
            $dataDetailExits = $this->findByPhaseID($phaseID);
            if ($dataDetailExits != $detail) {
                $this->deleteByPhaseID($phaseID);
                $budgetDetailInsert = [];
                foreach ($detail as $month => $budget) {
                    array_push($budgetDetailInsert, [
                        'phase_id' => $phaseID,
                        'month' => Carbon::parse('01-'.$month)->startOfMonth(),
                        'budget' => $budget,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }
                $this->budgetDetail->insert($budgetDetailInsert);
            }
        });
    }

    /**
     * @param $phaseID
     * @return array
     */
    public function findByPhaseID($phaseID)
    {
        $budgetDetailItem = $this->budgetDetail
            ->where('phase_id', $phaseID)
            ->orderBy('month')
            ->get(['month', 'budget']);
        return Arr::pluck($budgetDetailItem, 'budget', 'month');
    }

    /**
     * @param $phaseID
     * @return mixed
     */
    public function deleteByPhaseID($phaseID)
    {
        return $this->budgetDetail->where('phase_id', $phaseID)
            ->delete();
    }

    /**
     * @param $month
     * @param $projectID
     */
    public function getBudgetByMonthAndProjectID($month, $projectID)
    {
        $year = Carbon::parse($month)->format('Y');
        $month = Carbon::parse($month)->format('m');
        $phaseIDs = $this->phase->where('project_id', $projectID)
            ->pluck('id');
        return $this->budgetDetail
            ->whereIn('phase_id', $phaseIDs)
            ->whereMonth('month', $month)
            ->whereYear('month', $year)
            ->sum('budget');
    }
}
