<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $table = 'card_content';

    protected $fillable = ['content_type', 'content_id', 'position'];

    public function getContent () {
        
        return $this->morphTo('content');

    }
}
