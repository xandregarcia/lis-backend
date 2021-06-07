<?php

namespace App\Http\Resources\Publication;

use Illuminate\Http\Resources\Json\JsonResource;

class PublicationListResource extends JsonResource
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
            'ordinance_id' => $this->ordinance_id,
            'title' => $this->ordinances->title,
            'publisher' => $this->publishers,
            'first_publication' => (is_null($this->first_publication))?null:$this->first_publication
            'second_publication' => (is_null($this->second_publication))?null:$this->second_publication
            'third_publication' => (is_null($this->third_publication))?null:$this->third_publication
            'date_created' => $this->created_at
        ];
    }
}
