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
        return [
            'resolution_no' => $this->id,
            'subject' => (is_null($this->for_referral))?null:$this->for_referral->subject,
            'date_endorsed' => (is_null($this->for_referral->endorsement))?null:$this->for_referral->endorsement->date_endorsed,
            'meeting_date' => (is_null($this->for_referral->committee_report))?null:$this->for_referral->committee_report->meeting_date,
            'date_reported' => (is_null($this->for_referral->committee_report))?null:$this->for_referral->committee_report->agenda_date,
            'author' => $this->bokals,
            'date_passed' => $this->date_passed,
            'file' => env('APP_URL').Storage::url($this->file),
        ];
    }
}
