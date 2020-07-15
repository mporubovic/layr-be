<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = ['extension', 'path', 'original_name', 'size'];

    const CREATED_AT = 'uploaded_at';
    
    public function addEagerConstraints(array $cards)
    {
        $this->query
            ->whereIn(
                'card_content.card_id',
                collect($cards)->pluck('id')
            );
    }
}
