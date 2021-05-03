<?php

namespace App\Http\Resources\ForReferral;

use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Facades\Storage;

class ForReferralListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $committees = $this->committees()->get(['committees.id', 'committees.name']); # All
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
            'subject' => $this->subject,
            'receiving_date' => $this->receiving_date,
            'category' => (is_null($this->category))?null:$this->category->name,
            'origin' => (is_null($this->origin))?null:$this->origin->name,
            'agenda_date' => $this->agenda_date,
            'lead_committee' => (is_null($lead_committee))?null:$lead_committee,
            'joint_committees' => (is_null($joint_committees))?null:$joint_committees,
            'file' => env('APP_URL').Storage::url($this->file),
            'date_created' => $this->created_at
        ];
    }
}
