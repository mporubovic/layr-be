<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subdomain extends Model
{
    protected $fillable = ['name', 'settings'];
    protected $casts = ['settings' => 'json'];

    public function users() {
        return $this->belongsToMany('App\User')->withTimestamps();
    }

    public function boards(){
        return $this->belongsToMany('App\Models\Board')->withTimestamps();
    }
}
