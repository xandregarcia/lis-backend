<?php

namespace App\Http\Resources\Endorsement;

use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Facades\Storage;

class EndorsementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $committees = $this->for_referral->committees; # All
        $lead_committee = $committees->filter(function ($committee) {
            return $committee->pivot->lead_committee === 1;
        })->values()->first();
        $joint_committees = $committees->filter(function ($committee) {
            return $committee->pivot->joint_committee === 1;
        })->values();
        $joint_committees = $joint_committees->map(function ($joint_committee) {
            return [
                'id' => $joint_committee['id'],
                'name' => $joint_committee['name'],
            ];
        });

        return [
            'id' => $this->id,
            'for_referral_id'=> $this->for_referral_id,
            'subject' => (is_null($this->for_referral))?null:$this->for_referral->subject,
            'date_endorsed' => $this->date_endorsed,
            'lead_committee' => (is_null($lead_committee))?null:$lead_committee->id,
            'lead_committee_name' => (is_null($lead_committee))?null:$lead_committee->name,
            'joint_committees' => (is_null($joint_committees))?null:$joint_committees,
            'committees' =>  $committees,
            'file' => $this->file,
            'view' => "http://sp.dts/".Storage::url($this->file),
        ];
    }
}
