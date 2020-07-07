<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stack extends Model
{
    public function user() {

        return $this->belongsTo('App\User');

    }

    public function board() {
        
        return $this->belongsToMany('App\Models\Board');

    }

    public function cards() {
        
        return $this->belongsToMany('App\Models\Card');

    }
}
