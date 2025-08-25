<?php

namespace App\Http\Controllers\API\RH\RHParams;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    use HasQueryFilters;

    public function index(Request $request)
    {
        $query = Division::query();
        $result = $this->applyQueryParameters($query, $request);
        return response()->json($result);
    }

    public function listNames()
    {
        // $divisions = Division::all();
        // return NameResource::collection($divisions);
        return Division::select('id','nom')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:divisions,nom',
        ]);

        $division = Division::create($validated);
        return response()->json($division, 201);
    }

    public function show(Division $division)
    {
        return response()->json($division);
    }

    public function update(Request $request, Division $division)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:divisions,nom,' . $division->id,
        ]);

        $division->update($validated);
        return response()->json($division);
    }

    public function destroy(Division $division)
    {
        $division->delete();
        return response()->json(null, 204);
    }
}
