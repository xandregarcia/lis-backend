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
        $groups = $this->groups()->get(['groups.id', 'groups.name']); # All
        $chairman = $groups->filter(function ($group) {
            return $group->pivot->chairman === 1;
        })->values()->first();
        $vice_chairman = $groups->filter(function ($group) {
            return $group->pivot->vice_chairman === 1;
        })->values()->first();
        $members = $groups->filter(function ($group) {
            return $group->pivot->member === 1;
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
