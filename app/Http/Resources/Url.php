<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Url extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'url' => [
                'contentId' => $this->id,
                'path' => $this->path,
                'name' => $this->name,
                'position' => $this->content_position,
            ],

            'meta' => [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]
        ];
    }
}
