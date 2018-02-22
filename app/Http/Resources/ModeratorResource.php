<?php

namespace App\Http\Resources;

use App\User;
use Illuminate\Http\Resources\Json\Resource;

class ModeratorResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'user' => new UserResource(
                User::find($this->id)
            ),

            'role' => $this->pivot->role,
        ];
    }
}
