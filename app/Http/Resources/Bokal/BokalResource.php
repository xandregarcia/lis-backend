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

        $active = "";
        if($this->active === 'true'){
            $active = 'Active';
        }else{
            $active = 'Inactive';
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'active' => $active,
        ];
    }
}
