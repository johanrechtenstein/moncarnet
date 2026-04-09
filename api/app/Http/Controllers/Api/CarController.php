<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index(Request $request)
    {
        // Car::with('...') : C'est un constructeur de requête (Query Builder). 
        // C'est comme si vous écriviez le début d'une phrase. 
        // Le ->get() à la fin est le point final qui dit "Exécute la requête SQL maintenant".
        $cars = $request->user()->cars()->with('maintenances')->get();

        // On les renvoie au format JSON
        return response()->json($cars);
    }


    public function show($id)
    {
        // On récupère la voiture avec ses maintenances triées par date
        // On utilise findOrFail pour renvoyer une 404 si l'ID n'existe pas
        $car = auth()->user()->cars()->with(['maintenances' => function($query) {
        $query->orderBy('date', 'desc');
    }])->findOrFail($id);

        return response()->json([
            'data' => $car
        ], 200);
    }


    public function store(Request $request)
    {
        // 1. Validation des champs (selon votre SQL)
        $validated = $request->validate([
            'marque'          => 'required|string|max:100',
            'modele'          => 'required|string|max:100',
            'immatriculation' => 'required|string|unique:cars',
            //'user_id'         => 'required|exists:users,id', Vérifie que l'user existe
            'image_url'       => 'nullable|string'
        ]);

        // 2. Création
        $car = $request->user()->cars()->create($validated);

        // 3. Réponse
        return response()->json($car, 201);
    }



    public function destroy($id)
    {
        $car = auth()->user()->cars()->findOrFail($id);
        $car->delete();

        return response()->json(['message' => 'Voiture et ses maintenances supprimées'], 200);
    }


    public function update(Request $request, $id)
    {
        $car = $request->user()->cars()->findOrFail($id);

        $validated = $request->validate([
            // On vérifie l'unique, mais on ignore l'ID actuel pour ne pas bloquer si on ne change pas la plaque
            'immatriculation' => 'sometimes|string|unique:cars,immatriculation,' . $id,
            'image_url'       => 'nullable|string',
        ]);

        $car->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Véhicule mis à jour avec succès',
            'car' => $car
        ], 200);
}

}
