<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Personnel extends Model
{
    use HasFactory;

    protected $table = 'personnels';


    protected $fillable = [
        'matriculation',
        'cin',
        'societe_id',
        'direction_id',
        'fonction_id',
        'region_id',
        'zone_id',
        'site_id',
        'departement_id',
        'grade_id',
        'division_id',
        'centre_cout_id',
        'user_id',
        'carte_carburant_id',
        'tel',
        'superviseur',
        'titre',
        'adresse',
        'type',
        'sexe',
        'num_permis',
        'delivre_le',
        'fin_validite'
    ];


    protected $casts = [
        'delivre_le' => 'date',
        'fin_validite' => 'date',
        'num_carte_carb' => 'integer',
    ];

    // Relationships
    public function societe()
    {
        return $this->belongsTo(Societe::class);
    }

    public function direction()
    {
        return $this->belongsTo(Direction::class);
    }

    public function fonction()
    {
        return $this->belongsTo(Fonction::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function centreCout()
    {
        return $this->belongsTo(CentreCout::class);
    }

    public function carteCarburant()
    {
        return $this->belongsTo(carteCarburant::class);
    }

    public function User()
    {
        return $this->belongsTo(User::class);
    }
}
