<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Board as BoardResource;

class Stack extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public static $wrap = 'stack';
    
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'info' => [
                'id' => $this->id,
                'title' => $this->title,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],

            'boards' => BoardResource::collection($this->whenLoaded('boards')),
        ];
    }
}
