<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Text extends JsonResource
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
            
            'id' => $this->id,
            'meta' => [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at, // from card_content table
            ],

            'text' => [
                
                'text' => $this->text,

            ],
            
        ];
    }
}