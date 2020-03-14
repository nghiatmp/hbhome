<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BudgetDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'phase_id', 'month', 'budget'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     **/
    public function phase()
    {
        return $this->belongsTo('\App\Models\Phase');
    }

    /**
     * @param $month
     * @return string
     */
    public function getMonthAttribute($month)
    {
        return Carbon::parse($month)->format(config('constant.MONTH_FORMAT'));
    }
}
