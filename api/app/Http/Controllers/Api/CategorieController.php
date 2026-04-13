<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    public function index()
    {
        // On récupère juste l'ID et le NOM pour le menu déroulant
        return response()->json(Categorie::all());
    }
}
