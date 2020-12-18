<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stack extends Model
{
    protected $fillable = ['title', 'user_id'];
    
    public function users() {

        return $this->belongsToMany('App\User');

    }

    public function boards() {
        
        return $this->belongsToMany('App\Models\Board')->withTimestamps()->withPivot('position');

    }
}
