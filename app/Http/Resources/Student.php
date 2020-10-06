<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Student extends JsonResource
{
    public static $wrap = 'student';

    public function toArray($request)
    {
        return [
            'email' => $this->email,
            'role' => $this->roles->first()->name,
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'boards' => Board::collection($this->whenLoaded('boards')),
        ];
    }
}
