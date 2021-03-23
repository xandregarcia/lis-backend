<?php

namespace App\Http\Resources\Agency;

use Illuminate\Http\Resources\Json\JsonResource;

class AgencyListResource extends JsonResource
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
            'date_created' => $this->created_at
        ];
    }
}
