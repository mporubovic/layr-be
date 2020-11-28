<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\Stack as StackResource;


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
            'stacks' => StackResource::collection($this->whenLoaded('stacks')),
            'email_verified_at' => $this->email_verified_at,
        ];
    }
}
