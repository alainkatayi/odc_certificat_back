<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class FormationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        Carbon::setLocale('fr');
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "certificat_file" => $this->certificat_file,
            "participant_file" => $this->participant_file,
            "start_date" => $this->start_date,
            "end_date" => $this->end_date,
        
        ];
    }
}
