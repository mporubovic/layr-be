<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $fillable = ['title', 'owner'];

    public function path() {
        return route('boards.show', $this);
    }

    public function user() {

        return $this->belongsTo('App\User');


    }

    public function stacks() {

        return $this->belongsToMany('App\Models\Stack');

    }
}
