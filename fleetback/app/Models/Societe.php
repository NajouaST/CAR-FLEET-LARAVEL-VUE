<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Societe extends Model
{
    use HasFactory;

    protected $table = 'societes';

    protected $fillable = [
        'nom',
        'description',
        'logo_path',
    ];

    public function getLogoPathAttribute()
    {
        if (isset($this->attributes['logo_path']) && $this->attributes['logo_path']) {
            // Return the full URL without the /storage/ prefix
            return asset('storage/' . $this->attributes['logo_path']);
        }
        return null;
    }
}
