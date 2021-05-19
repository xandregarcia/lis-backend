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
        if($this->active === true){
            $active = 'Active';
        }else{
            $active = 'Inactive';
        }

        return [
            'id' => $this->id,
            'name' => "Hon. ".$this->first_name." ".$this->middle_name." ".$this->last_name,
            'status' => $active,
            'active' => $this->active,
            'date_created' => $this->created_at
        ];
    }
}
