<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'author'        => $this->author,
            'source'        => $this->source,
            'category'      => $this->category,
            'description'   => $this->description,
            'url'           => $this->url,
            'published_at'  => optional($this->published_at)->toDateTimeString(),
            'created_at'    => $this->created_at?->toDateTimeString(),
        ];
    }
}
