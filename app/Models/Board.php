<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Board extends Model
{
    protected $fillable = ['title', 'settings'];
    protected $casts = ['settings' => 'json'];


    // public function path() {
    //     return route('boards.show', $this);
    // }

    public function users() {

        return $this->belongsTo('App\User');


    }

    public function stacks() {

        return $this->belongsToMany('App\Models\Stack')->withTimestamps()->withPivot('position');

    }

    public function tags() {
        return $this->belongsToMany('App\Models\Tag')->withTimestamps();
    }

    public function cards() {
        
        return $this->belongsToMany('App\Models\Card')->withTimestamps();

    }
}
