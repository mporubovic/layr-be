<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\File as FileResource;
use App\Http\Resources\Content as ContentResource;
use App\Http\Resources\ContentCollection as ContentResourceCollection;

class Card extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    
    public static $wrap = 'cards';
    
    public function toArray($request)
    {
        // return parent::toArray($request);
        
        
        // return 'hello';
        
        return [
            'attributes' => [
                'id' => $this->id,
                'title' => $this->title,
            ],

            // 'content' => new ContentCollection($this->contents),
            'content' => $this->when(($request->mode == 'deep'), function () {
                return ContentResource::collection($this->content());
            })
            
        ];
            
    }
}
