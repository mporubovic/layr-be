<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $fillable = ['title'];

    // public function path() {
    //     return route('boards.show', $this);
    // }

    public function users() {

        return $this->belongsToMany('App\User')->withTimestamps();


    }

    public function stacks() {

        return $this->belongsToMany('App\Models\Stack')->withTimestamps();

    }
}
