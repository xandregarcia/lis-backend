<?php

namespace App\Http\Resources\Committee;

use Illuminate\Http\Resources\Json\JsonResource;

class CommitteeListResource extends JsonResource
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
        $members = $members->map(function ($member) {
            return [
                'id' => $member['id'],
                'name' => $member['name'],
            ];
        });

        return [
            'id' => $this->id,
            'name' => $this->name,
            'chairman' => (is_null($chairman))?null:$chairman,
            'vice_chairman' => (is_null($vice_chairman))?null:$vice_chairman,
            'members' => (is_null($members))?null:$members,
            'date_created' => $this->created_at
        ];
    }
}
