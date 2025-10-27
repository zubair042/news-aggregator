<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPreferenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'user_id'    => $this->user_id,
            'sources'    => $this->sources ? (is_array($this->sources) ? $this->sources : json_decode($this->sources, true)) : [],
            'categories' => $this->categories ? (is_array($this->categories) ? $this->categories : json_decode($this->categories, true)) : [],
            'authors'    => $this->authors ? (is_array($this->authors) ? $this->authors : json_decode($this->authors, true)) : [],
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
