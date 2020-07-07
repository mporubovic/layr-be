<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'card_content';

    public function content () {
        
        return $this->morphTo();

    }
}
