<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Embed extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'position' => $this->content_position,
            'id' => $this->id,
            'meta' => [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at, // from card_content table
            ],

            'embed' => [
                
                'path' => $this->path,
                // 'name' => $this->name,

            ],
            
        ];
    }
}
