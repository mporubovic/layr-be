<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $fillable = ['body', 'completed_at'];
}
