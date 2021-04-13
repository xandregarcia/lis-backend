<?php

namespace App\Http\Resources\ForReferral;

use Illuminate\Http\Resources\Json\JsonResource;

class ForReferralListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $committees = $this->committees()->get(['committees.id', 'committees.name']); # All
        $lead_committee = $committees->filter(function ($committee) {
            return $committee->pivot->lead_committee === 1;
        })->values()->first();
        $joint_committee = $committees->filter(function ($committee) {
            return $committee->pivot->joint_committee === 1;
        })->values();

        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'receiving_date' => $this->receiving_date,
            'category' => (is_null($this->category))?null:$this->category->name,
            'origin' => (is_null($this->origin))?null:$this->origin->name,
            'agenda_date' => $this->agenda_date,
            'lead_committee' => $lead_committee,
            'joint_committee' => $joint_committee,
            'file' => $this->file,
            'date_created' => $this->created_at,
        ];
    }
}
