<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    public function toArray($request) : array
    {
        return [
            'title' => $this->title,
            'slug' => route('news_item', ['slug' => $this->slug]),
            'description' => $this->description,
            'text' => $this->text,
            'published_at' => $this->published_at,
        ];
    }
}
