<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModeleResource extends JsonResource
{

    protected function marqueData()
    {
        return $this->marque ? [
            'id'        => $this->marque->id,
            'name'      => $this->marque->name,
            'image_url' => $this->marque->image_url,
        ] : null;
    }

    protected function gammeData()
    {
        return $this->gamme ? [
            'id'   => $this->gamme->id,
            'name' => $this->gamme->name,
        ] : null;
    }

    protected function typeCompteurData()
    {
        return $this->typeCompteur ? [
            'id'   => $this->typeCompteur->id,
            'name' => $this->typeCompteur->name,
        ] : null;
    }

    protected function typeCarburantData()
    {
        return $this->typeCarburant ? [
            'id'   => $this->typeCarburant->id,
            'name' => $this->typeCarburant->name,
        ] : null;
    }

    public function toArray($request)
    {
        // Modes: minimal, grid, full
        $mode = $this->additional['mode'] ?? 'full';

        if ($mode === 'mini') {
            return [
                'id'   => $this->id,
                'name' => $this->name,
            ];
        }

        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'puissance_cv'      => $this->puissance_cv,
            'puissance_din'     => $this->puissance_din,
            'places'            => $this->places,
            'poids_vide'        => $this->poids_vide,
            'poids_tc'          => $this->poids_tc,
            'charge_utile'      => $this->charge_utile,
            'cylindre'          => $this->cylindre,
            'consommation_min'  => $this->consommation_min,
            'consommation_max'  => $this->consommation_max,
            'consommation_moy'  => $this->consommation_moy,

            'marque'           => $this->marqueData(),
            'gamme'            => $this->gammeData(),
            'type_compteur'    => $this->typeCompteurData(),
            'type_carburant'   => $this->typeCarburantData(),
        ];
    }
}
