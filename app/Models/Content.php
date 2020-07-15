<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// class Content extends Model
class Content extends Model
{
    protected $table = 'card_content';

    protected $fillable = ['content_type', 'content_id', 'content_title', 'position'];

    public function getContent () {
        
        return $this->morphTo('content');

    }

    public function files () {

        // dd ($this->hasManyThrough('App\Models\File', 'App\Models\Content', 'content_id', 'id', 'content_id', 'content_id')->where('content_type', 'file')->toSql());

        return File::join('card_content', 'card_content.content_id', '=', 'files.id')
                ->where('card_content.content_type', '=', 'file')
                ->get();
        
    }
}
