<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     **/
    public function projects()
    {
        return $this->hasMany('\App\Models\Project');
    }
}
