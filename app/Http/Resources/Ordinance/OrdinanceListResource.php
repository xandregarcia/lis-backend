<?php

namespace App\Http\Resources\Ordinance;

use Illuminate\Http\Resources\Json\JsonResource;

class OrdinanceListResource extends JsonResource
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
                'name' => 'Hon. '.$author['first_name'].' '.$author['middle_name'].' '.$author['last_name']
            ];
        });

        $co_authors = $bokals->filter(function ($bokal) {
            return $bokal->pivot->co_author === 1;
        })->values();
        $co_authors = $co_authors->map(function ($co_author) {
            return [
                'id' => $co_author['id'],
                'name' => 'Hon. '.$author['first_name'].' '.$author['middle_name'].' '.$author['last_name']
            ];
        });
        if(is_null($this->for_referral)){
            $agenda_date = 'N/A';
            $date_endorsed = 'N/A';
            $meeting_date = 'N/A';
            $committee_report = 'N/A';
            $second_reading = 'N/A';
            $third_reading = 'N/A';
        }else{
            $agenda_date = $this->for_referral->agenda_date;
            $date_endorsed = (is_null($this->for_referral->endorsement))?"N/A":$this->for_referral->endorsement->date_endorsed;
            $meeting_date = (is_null($this->for_referral->committee_report))?"N/A":$this->for_referral->committee_report->meeting_date;
            $committee_report = (is_null($this->for_referral->committee_report))?"N/A":$this->for_referral->committee_report->agenda_date;
            $second_reading = (is_null($this->for_referral->second_reading))?$agenda_date:$this->for_referral->second_reading->agenda_date;
            $third_reading = (is_null($this->for_referral->third_reading))?$agenda_date:$this->for_referral->third_reading->agenda_date;
        }

        $publication = (is_null($this->publication))?null:$this->publication;
        $status = (is_null($this->for_referral))?null:$this->for_referral->comm_status;
        if(is_null($publication)){
            if($status != null){
                if($status->published == 1) {
                    $date_publised = 'N/A';
                    $publisher = 'N/A';
                }else {
                    $date_publised = 'For Publication';
                    $publisher = 'For Publication';
                }
            }else {
                $date_publised = 'N/A';
                $publisher = 'N/A';
            }
        }else{
            $date_publised = $publication->first_publication;
            $publisher = $publication->publishers->name;
        }
        

        return [
            'id' => $this->id,
            'ordinance_no' => $this->ordinance_no,
            'title' => $this->title,
            'amending' => $this->amending,
            'first_reading' => $agenda_date,
            'date_endorsed' => $date_endorsed,
            'meeting_date' => $meeting_date,
            'committee_report' => $committee_report,
            'second_reading' => $second_reading,
            'third_reading' => $third_reading,
            'authors' => $authors,
            'co_authors' => $co_authors,
            'date_passed' => $this->date_passed,
            'date_signed' => (is_null($this->date_signed))?null:$this->date_signed,
            'date_published' => $date_publised,
            'publisher' => $publisher,
            'date_created' => $this->created_at
        ];
    }
}
