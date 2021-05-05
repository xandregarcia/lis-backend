<?php

namespace App\Http\Resources\CommitteeReport;

use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Facades\Storage;

class CommitteeReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $committee_report = $this->for_referral->with(['committees'])->first();
        $committees = $committee_report->committees;
        $lead_committee = $committees->filter(function ($committee) {
             return $committee->pivot->lead_committee === 1;
        })->values()->first();
        $joint_committees = $committees->filter(function ($committee) {
            return $committee->pivot->joint_committee === 1;
        })->values();
        $joint_committees = $joint_committees->map(function ($joint_committee) {
            return [
                'id' => $joint_committee['id'],
                'name' => $joint_committee['name'],
            ];
        });

        return [
            'id' => $this->id,
            'subject' => (is_null($this->for_referral))?null:$this->for_referral->subject,
            'date_received' => $this->date_received,
            'agenda_date' => $this->agenda_date,
            'lead_committee' => (is_null($lead_committee))?null:$lead_committee->id,
            'joint_committees' => (is_null($joint_committees))?null:$joint_committees,
            'remarks' => $this->remarks,
            'meeting_date' => $this->meeting_date,
            'file' => "http://sp.dts/".Storage::url($this->file),
        ];
    }
}
