<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'key',
        'from_at',
        'to_at',
        'contract',
        'rank',
        'note',
        'budget',
        'team_id',
        'status',
        'css',
        'leakage',
        'ee',
        'timeliness',
        'backlog_key',
        'tms_key',
    ];

    protected $hidden = [
        'pivot',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     **/
    public function phases()
    {
        return $this->hasMany('\App\Models\Phase');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     **/
    public function team()
    {
        return $this->belongsTo('\App\Models\Team');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     **/
    public function users()
    {
        return $this->belongsToMany('\App\Models\User')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     **/
    public function resources()
    {
        return $this->hasMany('\App\Models\Resource');
    }
}
