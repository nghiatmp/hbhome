<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Holiday;

class HolidayService
{
    private $holiday;

    public function __construct(Holiday $holiday)
    {
        $this->holiday = $holiday;
    }

    public function getDates()
    {
        $holidays = $this->holiday->get(['from_at', 'to_at'])->toArray();
        $holidayDates = array();
        foreach ($holidays as $holiday) {
            $from = strtotime($holiday['from_at']);
            $to = strtotime($holiday['to_at']);
            $dateDiff = ($to-$from)/(60*60*24) + 1;
            for ($i = 0; $i < $dateDiff; $i++) {
                array_push($holidayDates, Carbon::parse($holiday['from_at'])->addDays($i));
            }
        }
        return $holidayDates;
    }

    /**
     * @return list holidays
     *
     */
    public function index($fromAt = null, $toAt = null)
    {
        $columns = ['id', 'title', 'from_at', 'to_at'];
        $holidays = $this->holiday
            ->when(!is_null($fromAt), function ($query) use ($fromAt) {
                $query->where('from_at', '>=', $fromAt);
            })
            ->when(!is_null($toAt), function ($query) use ($toAt) {
                $query->where('to_at', '<=', $toAt);
            })
            ->orderBy('from_at', 'desc')
            ->paginate(20, $columns, 'page')
            ->toArray();

        return [
            'data' => $holidays['data'],
            'current_page' => $holidays['current_page'],
            'per_page' => $holidays['per_page'],
            'last_page' => $holidays['last_page'],
            'total' => $holidays['total'],
        ];
    }

    /**
     * @param ['title', 'date'] $params
     *
     */
    public function store($params)
    {
        return $this->holiday->create($params);
    }

    /**
     * @param integer $id
     * @param ['title', 'date'] $params
     *
     */
    public function update($id, $params)
    {
        $holidayObject = $this->holiday->findOrFail($id);
        $holidayObject->update($params);

        return $holidayObject;
    }

    /**
     * @param integer $id
     */
    public function destroy($id)
    {
        $holidayObject = $this->holiday->findOrFail($id);
        return $holidayObject->delete();
    }

    /**
     * @param $from
     * @param $to
     * @return array
     */
    public function findHolidaysByDuration($from, $to)
    {
        $holidayDates = [];
        $holidays = $this->holiday
            ->where('from_at', '<=', $to)
            ->where('to_at', '>=', $from)
            ->get(['id', 'title', 'from_at', 'to_at'])
            ->toArray();

        foreach ($holidays as $holiday) {
            $from = strtotime($holiday['from_at']);
            $to = strtotime($holiday['to_at']);
            $dateDiff = ($to-$from)/(60*60*24) + 1;
            for ($i = 0; $i < $dateDiff; $i++) {
                array_push($holidayDates, Carbon::parse($holiday['from_at'])->addDays($i));
            }
        }
        return $holidayDates;
    }
}
