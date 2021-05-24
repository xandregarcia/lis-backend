<?php

namespace App\Http\Resources\Resolution;

use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Facades\Storage;

class ResolutionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $committee_report = $this->for_referral->with(['committees'])->first();
        $committees = $committee_report->committees;
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
            'title' => (is_null($this->for_referral))?null:$this->for_referral->subject,
            'agenda_date' => (is_null($this->for_referral))?null:$this->for_referral->agenda_date,
            'date_endorsed' => (is_null($this->for_referral->endorsements))?"N/A":$this->for_referral->endorsements->date_endorsed,
            'meeting_date' => (is_null($this->for_referral->committee_reports))?"N/A":$this->for_referral->committee_reports->meeting_date,
            'committee_report' => (is_null($this->for_referral->committee_reports))?"N/A":$this->for_referral->committee_reports->agenda_date,
            'bokal_id' => $this->bokal_id,
            'origin_name' => (is_null($this->for_referral->origin))?null:$this->for_referral->origin->name,
            'author' => "Hon. ".$this->bokals->first_name." ".$this->bokals->middle_name." ".$this->bokals->last_name,
            'lead_committee' => (is_null($lead_committee))?null:$lead_committee->id,
            'lead_committee_name' => (is_null($lead_committee))?null:$lead_committee->name,
            'joint_committees' => (is_null($joint_committees))?'N/A':$joint_committees,
            'date_passed' => $this->date_passed,
            'view' => env('APP_URL').Storage::url($this->file),
        ];
    }
}
