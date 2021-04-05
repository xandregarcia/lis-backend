<?php

namespace App\Http\Resources\Publisher;

use Illuminate\Http\Resources\Json\JsonResource;

class PublisherListResource extends JsonResource
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
            'head' => $this->head,
            'date_created' => $this->created_at
        ];
    }
}
