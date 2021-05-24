<?php

namespace App\Http\Resources\Resolution;

use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Facades\Storage;

class ResolutionResource extends JsonResource
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
        $subject = $for_referrals->map(function ($for_referral) {
            return[
                'subject' => $for_referral['subject']
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
            'resolution_no' => $this->resolution_no,
            'title' => $subject,
            'author' => "Hon. ".$this->bokals->first_name." ".$this->bokals->middle_name." ".$this->bokals->last_name,
            'lead_committee' => $committees['lead_committee']['name'],
            'joint_committees' => (is_null($committees['joint_committees']))?"N/A":$committees['joint_committees'],
            'date_passed' => $this->date_passed,
            'file' => $this->file,
            'view' => env('APP_URL').Storage::url($this->file),
        ];
    }
}
