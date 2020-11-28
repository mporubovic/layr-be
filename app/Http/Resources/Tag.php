<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

// use App\Http\Resources\Stack as StackkResouce;

class Tag extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    
    public static $wrap = 'tag';

    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'info' => [
                'id' => $this->id,
                'name' => $this->name,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],

            'settings' => $this->settings

        ];
    }
}
