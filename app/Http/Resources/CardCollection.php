<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Content as ContentResource;

class CardCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public static $wrap = 'cards';
    
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [

            'cards' => $this->collection,

        ];
    }
}
