<?php

namespace App\Http\Resources\Resolution;

use Illuminate\Http\Resources\Json\JsonResource;

class ResolutionListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'title' => (is_null($this->for_referral))?null:$this->for_referral->subject,
            'date_endorsed' => (is_null($this->for_referral->endorsement))?null:$this->for_referral->endorsement->date_endorsed,
            'meeting_date' => (is_null($this->for_referral->committee_report))?null:$this->for_referral->committee_report->meeting_date,
            'date_reported' => (is_null($this->for_referral->committee_report))?null:$this->for_referral->committee_report->agenda_date,
            'author' => "Hon. ".$this->bokals->first_name." ".$this->bokals->middle_name." ".$this->bokals->last_name,
            'date_passed' => $this->date_passed,
            'date_created' => $this->created_at
        ];
    }
}
