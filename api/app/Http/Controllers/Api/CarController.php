<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index()
    {
        // On récupère toutes les voitures de la base de données
        $cars = Car::all();

        // On les renvoie au format JSON
        return response()->json($cars);
    }
}
