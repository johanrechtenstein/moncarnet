<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = ['marque', 'modele', 'immatriculation', 'image_url', 'user_id'];
}
