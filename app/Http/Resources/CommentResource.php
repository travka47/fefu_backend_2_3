<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request) : array
    {
        return [
            'author' => new UserResource($this->user),
            'text' => $this->text,
            'created_at' => $this->created_at->format('d-m-Y H:i'),
            'updated_at' => $this->updated_at->format('d-m-Y H:i'),
        ];
    }
}
