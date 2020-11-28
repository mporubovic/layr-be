<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use App\Http\Resources\Tag as TagResource;

use App\Http\Resources\Card as CardResource;

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
            'info' => [
                'id' => $this->id,
                'title' => $this->title,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                // 'public' => $this->whenLoaded('subdomains', function() {
                //     // return $this->subdomains !== null;
                //     // return !empty($this->subdomains);
                //     return $this->subdomains->isNotEmpty();
                // }),
                // 'tags' => TagResource::collection($this->whenLoaded('tags')),
            ],

            'settings' => $this->settings,

            'cards' => CardResource::collection($this->whenLoaded('cards')),
        ];
    }
}
