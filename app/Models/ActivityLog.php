<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_log';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id', 'type', 'content', 'created_by'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     **/
    public function user()
    {
        return $this->belongsTo('\App\Models\User', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     **/
    public function project()
    {
        return $this->belongsTo('\App\Models\Project');
    }
}
