<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Relations\Relation;

use App\Http\Resources\File as FileResource;

class Content extends JsonResource
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
            // 'content' => $this->getContent,
            'title' => $this->content_title,
            'type' => $this->content_type,
            'position' => $this->position,
            
            'content' => $this->contentMorph($this->files),
            
            
        ];
    }

    public function contentMorph($content) {
        
        if ($content instanceof \App\Models\File) {
            return new FileResource($content);
        }

    }
}
