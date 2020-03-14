<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'from_at',
        'to_at',
        'status',
        'budget',
        'note',
        'css',
        'leakage',
        'ee',
        'timeliness',
        'project_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     **/
    public function project()
    {
        return $this->belongsTo('\App\Models\Project');
    }
}
