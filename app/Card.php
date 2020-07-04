<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    public function user() {

        return $this->belongsTo(User::class);
    }

    public function stack() {

        return $this->belongsToMany(Stack::class);

    }

    public function type() {

        return $this->morphTo('viewable');

    }
}
