<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserListResource extends JsonResource
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
            'name' => $this->firstname ." ". $this->middlename ." ". $this->lastname,
            'email' => $this->email,
            'group_id' => (is_null($this->group))?null:$this->group->id,
            'group_name' => (is_null($this->group))?null:$this->group->name,
            'date_created' => $this->created_at,
        ];
    }
}
