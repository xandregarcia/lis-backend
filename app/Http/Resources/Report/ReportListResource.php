<?php

namespace App\Http\Resources\Report;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $status = $this->comm_status;
        if(is_null($this->endorsements->first())) {
            if($status->passed == 1) {
                $endorsement = "N/A";
            }else{
                $endorsement = "Pending Endorsement";
            }
        }else{
            $endorsement = $this->endorsements->first()->date_endorsed;
        } 

        if(is_null($this->committee_reports->first())) {
            if($status->passed == 1) {
                $committee_report = "N/A";
                $committee_meeting = "N/A";
            }else{
                $committee_report = "Pending Committee Report";
                $committee_meeting = "Pending Committee Meeting";
            }
        }else{
            $committee_report = $this->committee_reports->first()->agenda_date;
            $committee_meeting = $this->committee_reports->first()->meeting_date;
        }

        $second_reading = (is_null($this->second_reading))?null:$this->second_reading->agenda_date;
        if(is_null($second_reading)) {
            if($status->passed == 1) {
                $second_reading = "N/A";
            }else{
                $second_reading = "Pending Second Reading";
            }
        }

        $third_reading = (is_null($this->third_reading))?null:$this->third_reading->agenda_date;
        if(is_null($third_reading)) {
            if($status->passed == 1) {
                $third_reading = "N/A";
            }else{
                $third_reading = "Pending Third Reading";
            }
        }

        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'agenda_date' => $this->agenda_date,
            'date_received' => $this->date_received,
            'origin' => $this->origin->name,
            'endorsement' => $endorsement,
            'committee_report' => $committee_report,
            'committee_meeting' => $committee_meeting,
            'first_reading' => $this->agenda_date,
            'second_reading' => $second_reading,
            'third_reading' => $third_reading,
            'date_approved' => $third_reading,
            'resolution_no' => (is_null($this->resolutions->first()))?"Pending":$this->resolutions->first()->resolution_no,
            'ordinance_no' => (is_null($this->ordinances))?"Pending":$this->ordinances->ordinance_no,
            'date_signed' => (is_null($this->ordinances))?"Pending":$this->ordinances->date_signed,
            'date_created' => $this->created_at
        ];
    }
}
