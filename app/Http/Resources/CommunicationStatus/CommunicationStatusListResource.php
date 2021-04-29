<?php

namespace App\Http\Resources\CommunicationStatus;

use Illuminate\Http\Resources\Json\JsonResource;

class CommunicationStatusListResource extends JsonResource
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
            'comm_status_id' => $this->id,
            'for_referral_id' => $this->for_referrals->id,
            'subject' => $this->for_referrals->subject,
            'date_agenda' => $this->for_referrals->agenda_date,
            'date_created' => $this->created_at,
        ];
    }
}
