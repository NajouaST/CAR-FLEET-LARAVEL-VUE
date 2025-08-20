<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modele extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'CO2',
        'Cylindre',
        'Poids',
        'marque_id',
        'gamme_id',
        'type_compteur_id',
        'type_carburant_id',
    ];

    // Relationships
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
