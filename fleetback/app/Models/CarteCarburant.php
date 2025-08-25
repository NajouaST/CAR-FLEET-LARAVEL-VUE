<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarteCarburant extends Model
{
    use HasFactory;

    protected $table = 'carte_carburants';

    protected $fillable = [
        'n_carte',
        'plafond_caburant',
        'unite',
        'plafond_service',
    ];
}
