<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stack extends Model
{
    
    protected $fillable = ['position'];
     
    public function user() {

        return $this->belongsTo('App\Models\User');

    }

    public function board() {
        
        return $this->belongsToMany('App\Models\Board');

    }

    public function cards() {
        
        return $this->belongsToMany('App\Models\Card');

    }
}
