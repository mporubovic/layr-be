<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subdomain extends Model
{
    protected $fillable = ['name'];

    public function users() {
        return $this->belongsToMany('App\User')->withTimestamps();
    }

    public function boards(){
        return $this->belongsToMany('App\Models\Board')->withTimestamps();
    }
}
