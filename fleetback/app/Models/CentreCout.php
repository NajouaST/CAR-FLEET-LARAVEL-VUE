<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CentreCout extends Model
{
    use HasFactory;

    protected $table = 'centre_couts';

    protected $fillable = [
        'nom',
    ];
}
