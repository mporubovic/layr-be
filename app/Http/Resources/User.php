<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    public static $wrap = 'user';

    public function toArray($request)
    {
        return [
            'email' => $this->email,
            'role' => $this->roles()->first()->name,
            'id' => $this->id,
        ];
    }
}
