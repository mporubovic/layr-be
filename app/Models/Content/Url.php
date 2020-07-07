<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    public function card() {

        return $this->morphMany(Card::class, 'viewable');
    }
}
