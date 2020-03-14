<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HideSetting extends Model
{
    protected $table = 'hide_setting';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'ids', 'created_by'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     **/
    public function user()
    {
        return $this->belongsTo('\App\Models\User', 'created_by');
    }
}
