<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Card as CardResource;

class Stack extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public static $wrap = 'stacks';
    
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'attributes' => [
                'id' => $this->id,
                'title' => $this->title,
            ],

            'cards' => CardResource::collection($this->cards),
        ];
    }
}
