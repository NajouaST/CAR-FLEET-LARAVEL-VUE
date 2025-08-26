<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarqueResource extends JsonResource
{
    public function toArray($request)
    {
        // Full representation
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'image_url'         => $this->image_url,
        ];
    }
}
