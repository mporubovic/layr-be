<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Card extends Model
{
    public function user() {

        return $this->belongsTo(User::class);

    }

    public function title() {

        return $this->belongsTo(User::class);

    }


    public function stack() {

        return $this->belongsToMany(Stack::class);

    }

    public function content() {

        // return $this->morphTo();
        // dd(array_search(static::class, Relation::morphMap()));
        
        if ($this->content_count === 1) {

            return $this->morphTo();

        } else if ($this->content_count >= 1) {
            return $this->hasManyThrough(app( ucfirst( $this->content_type )), Collection::class, 'collection_id', 'id', 'content_id', 'content_id');
        }
        
        
        // return $this->morphedByMany(Image::class, 'collection', 'collections', 'content_id');


    }    

    
    
    public function interpreter() {

        return $this->morphTo('interpreter');

    }

    public function content_count() {

        return $this->hasMany('content_count');

    }
}
