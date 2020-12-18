<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = ['extension', 'path', 'name', 'original_name', 'size', 'user_id'];

    const CREATED_AT = 'uploaded_at';
    const UPDATED_AT = null;
    
    // public function addEagerConstraints(array $cards)
    // {
    //     $this->query
    //         ->whereIn(
    //             'card_content.card_id',
    //             collect($cards)->pluck('id')
    //         );
    // }
}
