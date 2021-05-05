<?php

namespace App\Http\Resources\Ordinance;

use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Facades\Storage;

class OrdinanceResource extends JsonResource
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
            'ordinance_no' => $this->id,
            'title' => $this->title,
            'amending' => $this->amending,
            // 'author' => $this->author,
            'date_passed' => $this->date_passed,
            'date_signed' => $this->date_signed,
            'file' => env('APP_URL').Storage::url($this->file),
        ];
    }
}
