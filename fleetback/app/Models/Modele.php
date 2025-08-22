<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modele extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'puissance_cv',
        'puissance_din',
        'places',
        'poids_vide',
        'poids_tc',
        'charge_utile',
        'cylindre',
        'consommation_min',
        'consommation_max',
        'consommation_moy',
        'marque_id',
        'gamme_id',
        'type_compteur_id',
        'type_carburant_id',
    ];

    protected $casts = [
        'puissance_cv' => 'decimal:2',
        'puissance_din' => 'decimal:2',
        'poids_vide' => 'decimal:2',
        'poids_tc' => 'decimal:2',
        'charge_utile' => 'decimal:2',
        'cylindre' => 'decimal:2',
        'consommation_min' => 'decimal:2',
        'consommation_max' => 'decimal:2',
        'consommation_moy' => 'decimal:2',
    ];

    // Relations
    public function marque()
    {
        return $this->belongsTo(Marque::class);
    }

    public function gamme()
    {
        return $this->belongsTo(Gamme::class);
    }

    public function typeCompteur()
    {
        return $this->belongsTo(TypeCompteur::class);
    }

    public function typeCarburant()
    {
        return $this->belongsTo(TypeCarburant::class);
    }
}
