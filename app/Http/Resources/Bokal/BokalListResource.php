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
            'firstname' => $this->firstname,
            'middlename' => $this->middlename,
            'lastname' => $this->lastname,
            'active' => $active,
            'date_created' => $this->created_at
        ];
    }
}
