<?php

namespace App\Http\Controllers\API\params;

use App\Http\Controllers\Controller;
use App\Http\Resources\NameResource;
use App\Models\Fonction;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;

class FonctionController extends Controller
{
    use HasQueryFilters;

    public function index(Request $request)
    {
        $query = Fonction::query();
        $result = $this->applyQueryParameters($query, $request);
        return response()->json($result);
    }

    public function listNames()
    {
        // $fonctions = Fonction::all();
        // return NameResource::collection($fonctions);
        return Fonction::select('id','nom')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:fonctions,nom',
        ]);

        $fonction = Fonction::create($validated);
        return response()->json($fonction, 201);
    }

    public function show(Fonction $fonction)
    {
        return response()->json($fonction);
    }

    public function update(Request $request, Fonction $fonction)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:fonctions,nom,' . $fonction->id,
        ]);

        $fonction->update($validated);
        return response()->json($fonction);
    }

    public function destroy(Fonction $fonction)
    {
        $fonction->delete();
        return response()->json(null, 204);
    }
}