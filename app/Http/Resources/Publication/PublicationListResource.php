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
            'ordinance_no' => (is_null($this->ordinances))?null:$this->ordinances->ordinance_no,
            'title' => (is_null($this->ordinances))?null:$this->ordinances->title,
            'publisher_id' => $this->publisher_id,
            'publisher_name' => (is_null($this->publishers))?null:$this->publishers->name,
            'first_from' => (is_null($this->first_from))?null:$this->first_from,
            'first_to' => (is_null($this->first_to))?null:$this->first_to,
            'second_from' => (is_null($this->second_from))?null:$this->second_from,
            'second_to' => (is_null($this->second_to))?null:$this->second_to,
            'third_from' => (is_null($this->third_from))?null:$this->third_from,
            'third_to' => (is_null($this->third_to))?null:$this->third_to,
            'date_created' => $this->created_at
        ];
    }
}
