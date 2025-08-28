<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentVehicule extends Model
{
    protected $fillable = [
        'name',
        'note',
        'image',
        'vehicule_id',
    ];

    protected $appends = ['image_url'];
    /**
     * Accessor to return full URL for image.
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }
}
