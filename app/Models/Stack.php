<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stack extends Model
{
    public function user() {

        return $this->belongsTo(User::class);

    }

    public function board() {
        
        return $this->belongsToMany(Board::class);

    }

    public function cards() {
        
        return $this->hasMany(Card::class);

    }
}
