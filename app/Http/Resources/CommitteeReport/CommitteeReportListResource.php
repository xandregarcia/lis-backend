<?php

namespace App\Http\Resources\CommitteeReport;

use Illuminate\Http\Resources\Json\JsonResource;

class CommitteeReportListResource extends JsonResource
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
            'subject' => (is_null($this->for_referral))?null:$this->for_referral->subject,
            'date_received' => $this->date_received,
            'agenda_date' => $this->agenda_date,
            // 'committees' => $this->for_referral->committees->id,
            // 'joint_committee'=> $this->referral->joint_committee,
            'remarks' => $this->remarks,
            'meeting_date' => $this->meeting_date,
            'file' => $this->meeting_date,
            'date_created' => $this->created_at
        ];
    }
}
