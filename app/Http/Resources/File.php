<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class File extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public static $wrap = 'file';
    
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            
            
            'meta' => [
                'originalName' => $this->original_name,
                'extension' => $this->extension,
                'size' => $this->size,
                'uploaded_at' => $this->uploaded_at,
                'updated_at' => $this->updated_at, // from card_content table
            ],

            'file' => [
                'url' => $this->path,
                'position' => $this->content_position,
                'name' => $this->name,

            ],
            
        ];
    }
}
