<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marque extends Model
{
    protected $fillable = [
        'name',
        'image',
    ];

    protected $appends = ['image_url'];

    /**
     * Accessor to return full URL for image.
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function modeles() {
        return $this->hasMany(Modele::class);
    }
}
