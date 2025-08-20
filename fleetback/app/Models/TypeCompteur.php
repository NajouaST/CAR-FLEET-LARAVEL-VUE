<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeCompteur extends Model
{
    protected $fillable = [
        'name',
    ];

    public function modeles() {
        return $this->hasMany(Modele::class);
    }
}
