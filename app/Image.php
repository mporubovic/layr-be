<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    public function card() {

        return $this->morphMany(Card::class, 'viewable');
    }
}
