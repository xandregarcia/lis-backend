<?php

namespace App\Http\Resources\Committee;

use Illuminate\Http\Resources\Json\JsonResource;

class CommitteeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $bokals = $this->bokals()->get(['bokals.id', 'bokals.name']); # All
        $chairman = $bokals->filter(function ($bokal) {
            return $bokal->pivot->chairman === 1;
        })->values()->first();
        $vice_chairman = $bokals->filter(function ($bokal) {
            return $bokal->pivot->vice_chairman === 1;
        })->values()->first();
        $members = $bokals->filter(function ($bokal) {
            return $bokal->pivot->member === 1;
        })->values();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'chairman' => $chairman,
            'vice_chairman' => $vice_chairman,
            'members' => $members,
        ];
    }
}
