<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = ['marque', 'modele', 'immatriculation', 'image_url', 'user_id'];

    public function maintenances()
    {
        // Laravel va chercher dans la table 'maintenances' 
        // toutes les lignes qui ont un 'car_id' correspondant à cette voiture.
        return $this->hasMany(Maintenance::class,'car_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
