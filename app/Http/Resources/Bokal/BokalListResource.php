<?php

namespace App\Http\Resources\Bokal;

use Illuminate\Http\Resources\Json\JsonResource;

class BokalListResource extends JsonResource
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
            'active' => $this->active,
            'date_created' => $this->created_at
        ];
    }
}
