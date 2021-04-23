<?php

namespace App\Http\Resources\ForReferral;

use Illuminate\Http\Resources\Json\JsonResource;

class ForReferralResource extends JsonResource
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

        $joint_committee = $joint_committee->map(function ($joint_committees) {
            return [
                'id' => $joint_committees['id'],
                'name' => $joint_committees['name'],
            ];
        });

        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'receiving_date' => $this->receiving_date,
            'category_id' => (is_null($this->category))?null:$this->category->id,
            'origin_id' => (is_null($this->origin))?null:$this->origin->id,
            'agenda_date' => $this->agenda_date,
            'lead_committee' => (is_null($lead_committee))?null:$lead_committee->id,
            'joint_committee' => (is_null($joint_committee))?null:$joint_committee,
            'file' => $this->file,
        ];
    }
}
