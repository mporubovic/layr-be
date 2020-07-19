<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

// use App\Http\Resources\Stack as StackkResouce;

class Board extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    
    public static $wrap = 'board';

    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'attributes' => [
                'id' => $this->id,
                'title' => $this->title,
            ],

            'stacks' => Stack::collection($this->whenLoaded('stacks')),
        ];
    }
}
