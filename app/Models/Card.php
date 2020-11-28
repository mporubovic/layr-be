<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\Relation;

use App\Models\Content\File;
use App\Models\Content\Todo;
use App\Relations\FileRelation;
use App\Relations\TodoRelation;
use App\Relations\UrlRelation;
use App\Relations\EmbedRelation;
use App\Relations\TextRelation;
use App\Relations\WhiteboardRelation;
use App\User;



class Card extends Model
{

    protected $fillable = ['title', 'type', 'settings', 'program', 'x', 'y', 'width', 'height']; // CLEAN UP

    protected $casts = ['settings' => 'json'];

    public function user() {

        return $this->belongsTo(User::class);

    }

    public function boards() {
        return $this->belongsToMany(Board::class);
        
    }

    public function title() {

        return $this->belongsTo(User::class);

    }


    public function stacks() {

        return $this->belongsToMany(Stack::class)->withPivot('position', 'open')->withTimestamps();

    }

    public function contents () {

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

        
        // dd (self::content_type);
        // return $this->hasManyThrough(Relation::morphMap()[$this->content_type], 
        //                             'App\Models\Content\CardContent', 
        //                             'card_id', 
        //                             'id', 
        //                             'content_id', 
        //                             'content_id');
        //                             // ->orderBy('card_content.order');

        // $this->morphedByMany('App\Models\Content\Image', 'content');

        // return $this->belongsToMany(Relation::morphMap()[$this->content_type], 'card_content', 'card_id', 'content_id');
        // return $this->morphToMany('ssgs', 'sfsd');
        // return $this->belongsToMany('App\Models\Content\Image', 'card_content', 'card_id', 'content_id');
        
        // $t = $this->content_type;
        // dd($query);
        // return $this->belongsToMany($t, 'card_content', 'card_id', 'content_id')->withPivot('type');

        return $this->hasMany('App\Models\Content');

        // return $this->morphToMany();



    }    

    public function files () {

        // dd ($this->hasManyThrough('App\Models\File', 'App\Models\Content', 'content_id', 'id', 'content_id', 'content_id')->where('content_type', 'file')->toSql());

        // return File::join('card_content', 'card_content.content_id', '=', 'files.id')
        //         ->where('card_content.card_id', '=', $this->id)
        //         ->where('card_content.content_type', '=', 'file')
        //         ->get();

        // return File::join('card_content', 'card_content.content_id', '=', 'files.id')
        //         ->where('card_content.content_type', '=', 'file')
        //         ->get();

        return new FileRelation($this);
        
    }

    public function todos()
    {

        return new TodoRelation($this);
    }
    public function urls()
    {

        return new UrlRelation($this);
    }

    public function embeds()
    {

        return new EmbedRelation($this);
    }
    public function texts()
    {

        return new TextRelation($this);
    }    
    
    public function whiteboards()
    {

        return new WhiteboardRelation($this);
    }

    public function content () {

        return Content::where('card_content.card_id', '=', $this->id)->get();
        // return $this->hasMany('App\Models\Content');

    }

    
    
    public function interpreter() {

        // dd($this->morphTo('interpreter')->toSql());
        return $this->morphTo('interpreter');

    }

    public function content_count() {

        return $this->hasMany('content_count');

    }

    
}
