<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentVehiculeResource extends JsonResource
{

    protected function vehiculeData()
    {
        return $this->vehicule ? [
            'id'   => $this->vehicule->id,
        ] : null;
    }

    public function toArray($request)
    {
            return [
                'id'              => $this->id,
                'name' => $this->name,
                'note'   => $this->note,
                'image_url'   => $this->image_url,
                'vehicule'    => $this->vehiculeData(),
            ];
    }
}
