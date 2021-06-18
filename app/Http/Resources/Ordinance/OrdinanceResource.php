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
                'name' => "Hon. " . $author['first_name']." ".$author['middle_name']." ".$author['last_name']
            ];
        });

        $co_authors = $bokals->filter(function ($bokal) {
            return $bokal->pivot->co_author === 1;
        })->values();
        $co_authors = $co_authors->map(function ($co_author) {
            return [
                'id' => $co_author['id'],
                'name' => "Hon. " . $author['first_name']." ".$author['middle_name']." ".$author['last_name']
            ];
        });

        if(is_null($this->for_referral)) {
            $agenda_date = 'N/A';
            $date_endorsed = 'N/A';
            $meeting_date = 'N/A';
            $committee_report = 'N/A';
            $first_reading = 'N/A';
            $second_reading = 'N/A';
            $third_reading = 'N/A';
        } else {
            $agenda_date = $this->for_referral->agenda_date;
            $date_endorsed = (is_null($this->for_referral->endorsement))?"N/A":$this->for_referral->endorsement->date_endorsed;
            $meeting_date = (is_null($this->for_referral->committee_report))?"N/A":$this->for_referral->committee_report->meeting_date;
            $committee_report = (is_null($this->for_referral->committee_report))?"N/A":$this->for_referral->committee_report->agenda_date;
            $first_reading = $agenda_date;
            $second_reading = (is_null($this->for_referral->second_reading))?$agenda_date:$this->for_referral->second_reading->agenda_date;
            $third_reading = (is_null($this->for_referral->endorsement))?$agenda_date:$this->for_referral->third_reading->agenda_date;
        }

        return [
            'id' => $this->id,
            'ordinance_no' => $this->ordinance_no,
            'title' => $this->title,
            'amending' => $this->amending,
            'date_endorsed' => $date_endorsed,
            'meeting_date' => $meeting_date,
            'committee_report' => $committee_report,
            'first_reading' => $agenda_date,
            'second_reading' => $second_reading,
            'third_reading' => $third_reading,
            'authors' => $authors,
            'co_authors' => $co_authors,
            'date_passed' => $this->date_passed,
            'date_signed' => (is_null($this->date_signed))?null:$this->date_signed,
            'view' => env('STORAGE_URL').Storage::url($this->file),
        ];
    }
}
