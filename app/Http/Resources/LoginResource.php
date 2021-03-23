<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'token' => $this->token,
            'group_id' => (is_null($this->group))?null:$this->group->id,
            'group_name' => (is_null($this->group))?null:$this->group->name,
        ];
    }
}
