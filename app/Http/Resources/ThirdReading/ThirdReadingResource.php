<?php

namespace App\Http\Resources\ThirdReading;

use Illuminate\Http\Resources\Json\JsonResource;

class ThirdReadingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
