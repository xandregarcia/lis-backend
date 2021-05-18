<?php

namespace App\Http\Resources\Ordinance;

use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Facades\Storage;

class OrdinanceResource extends JsonResource
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
            'ordinance_no' => $this->id,
            'title' => $this->title,
            'amending' => $this->amending,
            'date_endorsed' => (is_null($this->for_referral->endorsement))?null:$this->for_referral->endorsement->date_endorsed,
            'meeting_date' => (is_null($this->for_referral->committee_report))?null:$this->for_referral->committee_report->meeting_date,
            'committee_report' => (is_null($this->for_referral->committee_report))?null:$this->for_referral->committee_report->agenda_date,
            'first_reading' => (is_null($this->for_referral))?null:$this->for_referral->agenda_date,
            'second_reading' => (is_null($this->for_referral->second_reading))?null:$this->for_referral->second_reading->agenda_date,
            'third_reading' => (is_null($this->for_referral->endorsement))?null:$this->for_referral->third_reading->agenda_date,
            'authors' => $authors,
            'co_authors' => $co_authors,
            'date_passed' => $this->date_passed,
            'date_signed' => $this->date_signed,
            'file' => env('APP_URL').Storage::url($this->file),
        ];
    }
}
