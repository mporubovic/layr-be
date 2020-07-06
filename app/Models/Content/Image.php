<?php

namespace App\Content;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    public function card() {

        return $this->morphToMany(Card::class, 'viewable');
    }
}
