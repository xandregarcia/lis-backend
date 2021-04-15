<?php

namespace App\Http\Resources\CommitteeReport;

use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Facades\DB;

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
        // $groups = $this->groups()->get(['groups.id', 'groups.name']); # All
        // $chairman = $groups->filter(function ($group) {
        //     return $group->pivot->chairman === 1;
        // })->values()->first();
        // $vice_chairman = $groups->filter(function ($group) {
        //     return $group->pivot->vice_chairman === 1;
        // })->values()->first();
        // $members = $groups->filter(function ($group) {
        //     return $group->pivot->member === 1;
        // })->values();

        $lead_committee = DB::table('committee_reports')
            ->join('committee_for_referral','committee_reports.for_referral_id','=','committee_for_referral.for_referral_id')
            ->join('committees','committee_for_referral.committee_id','committees.id')
            ->where('lead_committee','=', 1)
            ->where('committee_reports.id','=', $this->id)
            ->get(['committees.id','committees.name'])->first();
        
        $joint_committee = DB::table('committee_reports')
            ->join('committee_for_referral','committee_reports.for_referral_id','=','committee_for_referral.for_referral_id')
            ->join('committees','committee_for_referral.committee_id','committees.id')
            ->where('joint_committee','=', 1)
            ->where('committee_reports.id','=', $this->id)
            ->get(['committees.id','committees.name']);
        return [
            'id' => $this->id,
            'subject' => (is_null($this->for_referral))?null:$this->for_referral->subject,
            'date_received' => $this->date_received,
            'agenda_date' => $this->agenda_date,
            'lead_committee' => $lead_committee,
            'joint_committee'=> $joint_committee,
            'remarks' => $this->remarks,
            'meeting_date' => $this->meeting_date,
            'file' => $this->meeting_date,
            'date_created' => $this->created_at
        ];
    }
}
