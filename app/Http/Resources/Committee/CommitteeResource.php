<?php

namespace App\Http\Resources\Committee;

use Illuminate\Http\Resources\Json\JsonResource;

class CommitteeResource extends JsonResource
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
            'name' => $this->name,
            'chairman' =>(is_null($this->bokal))?null:$this->bokal->name,
            'vice_chairman' => (is_null($this->bokal))?null:$this->bokal2->name,
            'members' => $this->members,
        ];
    }
}
