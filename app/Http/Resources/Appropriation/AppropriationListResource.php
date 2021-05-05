<?php

namespace App\Http\Resources\Appropriation;

use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Facades\Storage;

class AppropriationListResource extends JsonResource
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
            'appropriation_no' => $this->id,
            'for_referral_id' => $this->for_referral_id,
            'title' => $this->title,
            'date_passed' => $this->date_passed,
            'date_created' => $this->created_at
        ];
    }
}