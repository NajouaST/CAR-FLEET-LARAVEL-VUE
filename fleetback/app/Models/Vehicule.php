<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicule extends Model
{
    use HasFactory;

    protected $table = 'vehicules';

    protected $fillable = [
        'carte_grise',
        'carte_grise_front',
        'carte_grise_back',

        // Identité
        'immatriculation',
        'chassis',
        'dmc',

        // Spécifications
        'couleur',
        'categorie',

        // Situation
        'situation',
        'statut',

        // Achat
        'formule_achat',
        'date',
        'valeur',
        'loyer',
        'date_garantie',
        'km_garantie',

        // Cession
        'date_cession',
        'valeur_cession',

        'assigned_to',

        // Foreign keys
        'modele_id',
        'famille_vehicule_id',
        'fournisseur_id',
    ];

    protected $casts = [
        'dmc' => 'date',
        'date' => 'date',
        'date_garantie' => 'date',
        'date_cession' => 'date',
        'valeur' => 'decimal:2',
        'loyer' => 'decimal:2',
        'km_garantie' => 'decimal:2',
        'valeur_cession' => 'decimal:2',
    ];

    protected $appends = ['carte_grise_front_url', 'carte_grise_back_url'];

    /**
     * Accessors for carte grise images
     */
    public function getCarteGriseFrontUrlAttribute()
    {
        return $this->carte_grise_front ? asset('storage/' . $this->carte_grise_front) : null;
    }

    public function getCarteGriseBackUrlAttribute()
    {
        return $this->carte_grise_back ? asset('storage/' . $this->carte_grise_back) : null;
    }

    /**
     * Relations
     */
    public function modele()
    {
        return $this->belongsTo(Modele::class);
    }

    public function familleVehicule()
    {
        return $this->belongsTo(FamilleVehicule::class);
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }
}
