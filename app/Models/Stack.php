<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stack extends Model
{
    
    protected $fillable = ['position'];
     
    public function user() {

        return $this->belongsTo('App\Models\User');

    }

    public function boards() {
        
        return $this->belongsToMany('App\Models\Board')->withTimestamps();

    }

    public function cards() {
        
        return $this->belongsToMany('App\Models\Card')->withPivot('position', 'open')->withTimestamps();

    }
}
