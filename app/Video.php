<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    public function card() {

        return $this->morphMany(Card::class, 'viewable');
    }
}
