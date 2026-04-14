<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $fillable = [
    'date', 
    'kilometrage', 
    'description', 
    'facture_url', 
    'est_obsolete', 
    'categorie_id', 
    'car_id',
    'parent_id',
    'echeance_km',   // On ajoute ça
    'echeance_date',
    'status',
];

public function car()
{
    return $this->belongsTo(Car::class, 'car_id');
}
public function categorie()
{
    return $this->belongsTo(Categorie::class, 'categorie_id');
}

}
