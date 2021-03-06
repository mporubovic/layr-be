<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    public function cards() {

        // return $this->name;
        return $this->hasMany('App\Models\Card');

    }

    public function stacks() {

        return $this->belongsToMany('App\Models\Stack')->withTimestamps();

    }    
    
    public function boards() {

        return $this->hasMany('App\Models\Board');
        
    }

    public function groups() {

        return $this->belongsToMany('App\Group')->withTimestamps();

    }

    public function roles() {

        return $this->belongsToMany('App\Role')->withTimestamps();

    }
    public function tags() {

        return $this->hasMany('App\Models\Tag');

    }

}
