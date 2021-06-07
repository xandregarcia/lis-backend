<?php

namespace App\Http\Resources\ForReferral;

use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Facades\Storage;

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

        if($this->comm_status->passed == 1 && $this->third_reading == null && $this->comm_status->type < 3){
            $third_reading = $this->agenda_date;
        }else if ($this->third_reading != null) {
            $third_reading = $this->third_reading->agenda_date;
        }else{
            $third_reading = "N/A";   
        }

        if($this->comm_status->type < 3 && $this->second_reading ==null && $this->comm_status->passed == 1){
            $second_reading = $this->agenda_date;
        }else if ($this->second_reading != null) {
            $second_reading = $this->second_reading->agenda_date;    
        }else{
            $second_reading = "N/A";
        }
        $endorsement = $this->endorsements->first();
        $committee_report = $this->committee_reports->first();

        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'date_received' => $this->date_received,
            'category_id' => (is_null($this->category))?null:$this->category->id,
            'category_name' => (is_null($this->category))?null:$this->category->name,
            'origin_id' => (is_null($this->origin))?null:$this->origin->id,
            'origin_name' => (is_null($this->origin))?null:$this->origin->name,
            'agenda_date' => $this->agenda_date,
            'lead_committee' => (is_null($lead_committee))?null:$lead_committee->id,
            'lead_committee_name' => (is_null($lead_committee))?null:$lead_committee->name,
            'joint_committees' => (is_null($joint_committee))?null:$joint_committee,
            'date_endorsed' => (is_null($endorsement))?'N/A':$endorsement->date_endorsed,
            'meeting_date' => (is_null($committee_report))?"N/A":$committee_report->meeting_date,
            'committee_report' => (is_null($committee_report))?"N/A":$committee_report->agenda_date,
            'second_reading' => $second_reading,
            'third_reading' => $third_reading,
            'file' => $this->file,
            'view' => env('STORAGE_URL').Storage::url($this->file),
        ];
    }
}
