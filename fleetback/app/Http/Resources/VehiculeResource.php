<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehiculeResource extends JsonResource
{
    protected function modeleData()
    {
        return $this->modele ? [
            'id'   => $this->modele->id,
            'name' => $this->modele->name,

            'puissance_cv' => $this->modele->puissance_cv,
            'puissance_din' => $this->modele->puissance_din,

            'places' => $this->modele->places,

            'poids_vide' => $this->modele->poids_vide,
            'poids_tc' => $this->modele->poids_tc,
            'charge_utile' => $this->modele->charge_utile,

            'cylindre' => $this->modele->cylindre,

            'consommation_min' => $this->modele->consommation_min,
            'consommation_max' => $this->modele->consommation_max,
            'consommation_moy' => $this->modele->consommation_moy,
        ] : null;
    }
    protected function modeleMinData()
    {
        return $this->modele ? [
            'id'   => $this->modele->id,
            'name' => $this->modele->name,
        ] : null;
    }

    protected function marqueData()
    {
        return $this->modele && $this->modele->marque ? [
            'id'        => $this->modele->marque->id,
            'name'      => $this->modele->marque->name,
            'image_url' => $this->modele->marque->image_url,
        ] : null;
    }

    protected function familleData()
    {
        return $this->familleVehicule ? [
            'id'   => $this->familleVehicule->id,
            'name' => $this->familleVehicule->name,
        ] : null;
    }

    protected function fournisseurData()
    {
        return $this->fournisseur ? [
            'id'   => $this->fournisseur->id,
            'name' => $this->fournisseur->name,
        ] : null;
    }

    public function toArray($request)
    {
        // Decide which representation to use
        $mode = $this->additional['mode'] ?? 'full';

        if ($mode === 'minimal') {
            return [
                'id'              => $this->id,
                'immatriculation' => $this->immatriculation,
                'assigned_to'   => $this->assigned_to,
                'renouvelable'    => $this->familleVehicule->renouvelable ?? null,
            ];
        }

        if ($mode === 'grid') {
            return [
                'id'              => $this->id,
                'immatriculation' => $this->immatriculation,
                'categorie'       => $this->categorie,
                'situation'       => $this->situation,
                'statut'          => $this->statut,
                'formule_achat'   => $this->formule_achat,
                'date'            => $this->date,
                'valeur'          => $this->valeur,
                'date_cession'    => $this->date_cession,
                'assigned_to'   => $this->assigned_to,

                'modele'      => $this->modeleMinData(),
                'marque'      => $this->marqueData(),
                'famille'     => $this->familleData(),
                'fournisseur' => $this->fournisseurData(),
            ];
        }

        // Full
        return [
            'id'               => $this->id,
            'immatriculation'  => $this->immatriculation,
            'chassis'          => $this->chassis,
            'dmc'              => $this->dmc,
            'couleur'          => $this->couleur,
            'categorie'        => $this->categorie,
            'situation'        => $this->situation,
            'statut'           => $this->statut,
            'formule_achat'    => $this->formule_achat,
            'date'             => $this->date,
            'valeur'           => $this->valeur,
            'loyer'            => $this->loyer,
            'date_garantie'    => $this->date_garantie,
            'km_garantie'      => $this->km_garantie,
            'date_cession'     => $this->date_cession,
            'valeur_cession'   => $this->valeur_cession,
            'assigned_to'   => $this->assigned_to,

            'carte_grise'   => $this->carte_grise,
            'carte_grise_front_url'   => $this->carte_grise_front_url,
            'carte_grise_back_url'   => $this->carte_grise_back_url,

            'modele'      => $this->modeleData(),
            'marque'      => $this->marqueData(),
            'famille'     => $this->familleData(),
            'fournisseur' => $this->fournisseurData(),
        ];
    }
}
