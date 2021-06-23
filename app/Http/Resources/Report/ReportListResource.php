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
        
        $endorsement = (is_null($this->endorsements))?null:$this->endorsements->first()->date_endorsed;
        if(is_null($endorsement)) {
            if($status->approved == 1) {
                $endorsement = "N/A";
            }else{
                $endorsement = "Pending Endorsement";
            }
        }

        $committee_report = (is_null($this->committee_reports))?null:$this->committee_reports->first()->agenda_date;
        $committee_meeting = (is_null($this->committee_reports))?null:$this->committee_reports->first()->meeting_date;
        if(is_null($committee_report)) {
            if($status->approved == 1) {
                $committee_report = "N/A";
                $committee_meeting = "N/A";
            }else{
                $committee_report = "Pending Committee Report";
                $committee_meeting = "Pending Committee Meeting";
            }
        }

        $second_reading = (is_null($this->second_reading))?null:$this->second_reading->agenda_date;
        if(is_null($second_reading)) {
            if($status->approved == 1) {
                $second_reading = "N/A";
            }else{
                $second_reading = "Pending Second Reading";
            }
        }

        $third_reading = (is_null($this->third_reading))?null:$this->third_reading->agenda_date;
        if(is_null($third_reading)) {
            if($status->approved == 1) {
                $third_reading = "N/A";
            }else{
                $third_reading = "Pending Third Reading";
            }
        }

        $ordinance_no = (is_null($this->ordinances))?"Pending":$this->ordinances->ordinance_no;

        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'date_received' => $this->date_received,
            'origin' => $this->origin->name,
            'endorsement' => $endorsement,
            'committee_report' => $committee_report,
            'committee_meeting' => $committee_meeting,
            'second_reading' => $second_reading,
            'third_reading' => $third_reading,
            'date_approved' => $third_reading,
            'ordinance_no' => (is_null($this->ordinances))?"Pending":$this->ordinances->ordinance_no,
            'date_signed' => (is_null($this->ordinances))?"Pending":$this->ordinances->date_signed,
            // 'committees' => $this->committees,
            'date_created' => $this->created_at
        ];
    }
}
