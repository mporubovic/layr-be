<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StackCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static $wrap = 'stacks';
    public function toArray($request)
    {
        // return parent::toArray($request);
        // return ['stacks' => $this->collection];
        return [$this->collection];
    }
}
