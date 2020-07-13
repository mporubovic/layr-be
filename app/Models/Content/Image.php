<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['name', 'path'];

    public function file() {


        // dd($this->morphMany('App\Models\File', 'content', 'content_type')->toSql());
        return($this->morphMany('App\Models\File', 'content', 'content_type'));
        // dd($this->morphedByMany('App\Models\File', 'images', 'content', 'content_id', 'content_id')->toSql());

    }
}
