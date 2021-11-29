<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request) : array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'text' => $this->text,
            'created_at' => $this->created_at->format('d-m-Y H:i'),
            'updated_at' => $this->updated_at->format('d-m-Y H:i'),
            'author' => new UserResource($this->user),
            'comments' => CommentResource::collection($this->comments)
        ];
    }
}
