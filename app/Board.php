<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $fillable = ['title', 'owner'];

    public function path() {
        return route('boards.show', $this);
    }

    public function user() {

        return $this->belongsTo(User::class);


    }

    public function stacks() {

        return $this->hasMany(Stack::class);

    }
}
