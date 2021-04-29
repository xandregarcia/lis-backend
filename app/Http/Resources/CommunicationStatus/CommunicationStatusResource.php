<?php

namespace App\Http\Resources\CommunicationStatus;

use Illuminate\Http\Resources\Json\JsonResource;

class CommunicationStatusResource extends JsonResource
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
            'approve' => $this->approve,
            'endorsement' => $this->endorsement,
            'committee_report' => $this->committee_report,
            'second_reading' => $this->second_reading,
            'third_reading' => $this->third_reading
        ];
    }
}
