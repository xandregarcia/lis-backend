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

        $for_referrals = $this->for_referral; # All
        $communication = $for_referrals->map(function ($for_referral) {
            return[
                'subject' => $for_referral['subject'],
                'for_referral_id'=> $for_referral['id']
            ];
        });
        $committees = $for_referrals->map(function ($for_referral) {
            $committees = $for_referral->committees;
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
                'lead_committee' => $lead_committee,
                'joint_committees' => $joint_committees
            ];
        })->first();

        return [
            'id' => $this->id,
            'communication' => $communication,
            'date_received' => $this->date_received,
            'agenda_date' => $this->agenda_date,
            'lead_committee' => $committees['lead_committee']['name'],
            'joint_committees' => (is_null($committees['joint_committees']))?'N/A':$committees['joint_committees'],
            'remarks' => $this->remarks,
            'meeting_date' => $this->meeting_date,
            'file' => $this->file,
            'view' => "http://sp.dts/".Storage::url($this->file),
        ];
    }
}
