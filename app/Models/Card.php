<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\Relation;



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

    public function content () {

        // return $this->morphTo();
        // dd(array_search(static::class, Relation::morphMap())); app( ucfirst( $this->content_type )) array_search(static::class, Relation::morphMap())
        
        
        // return $this->hasManyThrough(Content::class, Collection::class, 'collection_id', 'id', 'content_id', 'content_id');
        
        // return $this->hasManyThrough(Image::class, Content::class, 'a', 'b', 'c', 'd');

        // NON-DYNAMIC SINGLE MODEL return $this->morphedByMany(Image::class, 'content');
        
        // return $this->morphedByMany(Image::class, 'collection', 'collections', 'content_id');
        
        
        // DOCS 
        // The following solution does not allow for cards containing different Models
        // https://github.com/laravel/ideas/issues/1867
        //
        // card_id => card_id column on card_content
        // id => id column on Model table
        // content_id => content_id column on cards table
        // content_id => content_id column on card_content table
        // CardContent::class => intermediary placeholder Pivot type model, however table name is card_content
        // Relation::morphMap()[$this->content_type] references Relation::morhpMap() in AppServiceProvider.php
        //
        // OLD  ucfirst(app($this->content_type)) 
        // ucfirst() upper case first as content_type is stored in lowercase and Model names are ucfirst
        //
        //

        // dd(Relation::morphMap()['image']);

        
        return $this->hasManyThrough(Relation::morphMap()[$this->content_type], 
                                    'App\Content\CardContent', 
                                    'card_id', 
                                    'id', 
                                    'content_id', 
                                    'content_id');




    }    

    
    
    public function interpreter() {

        return $this->morphTo('interpreter');

    }

    public function content_count() {

        return $this->hasMany('content_count');

    }
}
