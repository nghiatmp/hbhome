<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name',
        'email',
        'role',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function tasks()
    {
        return $this->hasMany('\App\Models\Task');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     **/
    public function resources()
    {
        return $this->hasMany('\App\Models\Resource');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     **/
    public function teams()
    {
        return $this->belongsToMany('\App\Models\Team', 'user_team')
            ->withPivot('from', 'to');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     **/
    public function projects()
    {
        return $this->belongsToMany('\App\Models\Project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * current team
     */
    public function availableTeam()
    {
        return $this->teams()->wherePivot('to', null);
    }

    /**
     * @return array
     */
    public function getTeamsAttribute()
    {
        $userTeams = UserTeam::where('user_id', $this->id)
            ->whereNull('to')->get()->toArray();
        return Arr::pluck($userTeams, 'team_id');
    }
}
