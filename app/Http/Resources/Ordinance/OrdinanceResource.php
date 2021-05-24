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

        $bokals = $this->bokals()->get();

        $authors = $bokals->filter(function ($bokal) {
            return $bokal->pivot->author === 1;
        })->values();
        $authors = $authors->map(function ($author) {
            return [
                'id' => $author['id'],
            ];
        });

        $co_authors = $bokals->filter(function ($bokal) {
            return $bokal->pivot->co_author === 1;
        })->values();
        $co_authors = $co_authors->map(function ($co_author) {
            return [
                'id' => $co_author['id'],
            ];
        });

        $agenda_date = $this->for_referral->agenda_date;

        return [
            'ordinance_no' => $this->id,
            'title' => $this->title,
            'amending' => $this->amending,
            'date_endorsed' => (is_null($this->for_referral->endorsement))?"N/a":$this->for_referral->endorsement->date_endorsed,
            'meeting_date' => (is_null($this->for_referral->committee_report))?"N/A":$this->for_referral->committee_report->meeting_date,
            'committee_report' => (is_null($this->for_referral->committee_report))?"N/A":$this->for_referral->committee_report->agenda_date,
            'first_reading' => $agenda_date
            'second_reading' => (is_null($this->for_referral->second_reading))?$agenda_date:$this->for_referral->second_reading->agenda_date,
            'third_reading' => (is_null($this->for_referral->endorsement))?$agenda_date:$this->for_referral->third_reading->agenda_date,
            'authors' => $authors,
            'co_authors' => $co_authors,
            'date_passed' => $this->date_passed,
            'date_signed' => $this->date_signed,
            'view' => env('APP_URL').Storage::url($this->file),
        ];
    }
}
