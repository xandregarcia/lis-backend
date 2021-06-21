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
        if($this->active === true){
            $status = 'Active';
        }else{
            $status = 'Inactive';
        }

        if($this->active === true){
            $active = 1;
        }else{
            $active = 0;
        }

        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'status' => $status,
            'active' => $active,
        ];
    }
}
