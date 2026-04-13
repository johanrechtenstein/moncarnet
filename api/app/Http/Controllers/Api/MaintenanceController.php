<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use App\Models\Categorie;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validation
        $validated = $request->validate([
            'date'         => 'required|date',
            'kilometrage'  => 'required|integer',
            'description'  => 'required|string',
            'facture_url'  => 'nullable|string',
            'est_obsolete' => 'boolean',
            'categorie_id' => 'required|exists:categories,id',
            'car_id'       => 'required|exists:cars,id', // On vérifie que la voiture existe !
            'parent_id'    => 'nullable|exists:maintenances,id'
        ]);

        // 2. Création (Méthode "Directe")
        $maintenance = Maintenance::create($validated);

        return response()->json($maintenance->load('categorie'), 201);
    }


    public function destroy($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->delete();

        return response()->json(['message' => 'Maintenance supprimée'], 200);
        //si on ne veut pas avoir le retour => 204
    }



    public function update(Request $request, $id)
    {
        $maintenance = Maintenance::findOrFail($id);

        // 1. Validation des données entrantes
        $validated = $request->validate([
            'date'         => 'required|date',
            'kilometrage'  => 'required|integer|min:0',
            'description'  => 'required|string',
            'facture_url'  => 'nullable|string',
            'categorie_id' => 'required|exists:categories,id',
        ]);

    // 2. Vérification du délai de 24h
    // On compare la date de création (created_at) avec maintenant
    $isEditable = $maintenance->created_at->diffInHours(now()) < 24;

    if ($isEditable && !$maintenance->est_obsolete) {
        // CAS A : Moins de 24h -> On modifie la ligne existante
        $maintenance->update($validated);
        
        return response()->json([
            'message' => 'Maintenance corrigée (moins de 24h)',
            'data' => $maintenance
        ], 200);

    } else {
        // CAS B : Plus de 24h ou déjà obsolète -> On archive et on recrée
        
        // On marque l'ancienne comme obsolète
        $maintenance->update(['est_obsolete' => true]);

        // On crée la nouvelle version
        $newMaintenance = Maintenance::create([
            'date'         => $validated['date'],
            'kilometrage'  => $validated['kilometrage'],
            'description'  => $validated['description'],
            'facture_url'  => $validated['facture_url'],
            'categorie_id' => $validated['categorie_id'],
            'car_id'       => $maintenance->car_id,
            'parent_id'    => $maintenance->id, // Lien vers l'ancienne
            'est_obsolete' => false,
        ]);

        return response()->json([
            'message' => 'Maintenance mise à jour (historique créé car > 24h)',
            'new_entry' => $newMaintenance
        ], 201);
    }
}
}
