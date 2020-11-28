<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];
    protected $casts = ['settings' => 'json'];

    public function boards() {
        return $this->belongsToMany('App\Models\Board')->withTimestamps();

    }

}
