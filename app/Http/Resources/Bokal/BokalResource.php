<?php

namespace App\Http\Resources\Bokal;

use Illuminate\Http\Resources\Json\JsonResource;

class BokalResource extends JsonResource
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
            'middlename' => $this->middlename,
            'lastname' => $this->lastname,
            'active' => $this->active,
        ];
    }
}
