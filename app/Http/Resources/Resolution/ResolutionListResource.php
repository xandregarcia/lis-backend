<?php

namespace App\Http\Resources\Resolution;

use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Facades\Storage;

class ResolutionListResource extends JsonResource
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
            'resolution_no' => $this->id,
            'subject' => (is_null($this->for_referral))?null:$this->for_referral->subject,
            'author' => $this->author,
            'date_passed' => $this->date_passed,
            'file' => env('APP_URL').Storage::url($this->file),
            'date_created' => $this->created_at
        ];
    }
}
