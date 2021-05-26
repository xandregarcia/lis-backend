<?php

namespace App\Http\Resources\Endorsement;

use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Facades\Storage;

class EndorsementListResource extends JsonResource
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
        $for_referral = $for_referrals->map(function ($for_referral) {
            return[
                'subject' => $for_referral['subject'],
                'date_received' => $for_referral['date_received']
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
            'for_referrals' => $for_referral,
            'date_endorsed' => $this->date_endorsed,
            'lead_committee' => $committees['lead_committee']['name'],
            'joint_committees' => (is_null($committees['joint_committees']))?'N/A':$committees['joint_committees'],
            'date_created' => $this->created_at
        ];
    }
}
