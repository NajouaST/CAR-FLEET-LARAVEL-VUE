<?php

namespace App\Http\Controllers\API\params;

use App\Http\Controllers\Controller;
use App\Http\Resources\NameResource;
use App\Models\Grade;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    use HasQueryFilters;

    public function index(Request $request)
    {
        $query = Grade::query();
        $result = $this->applyQueryParameters($query, $request);
        return response()->json($result);
    }

    public function listNames()
    {
        // $grades = Grade::all();
        // return NameResource::collection($grades);
        return Grade::select('id','nom')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:grades,nom',
        ]);

        $grade = Grade::create($validated);
        return response()->json($grade, 201);
    }

    public function show(Grade $grade)
    {
        return response()->json($grade);
    }

    public function update(Request $request, Grade $grade)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:grades,nom,' . $grade->id,
        ]);

        $grade->update($validated);
        return response()->json($grade);
    }

    public function destroy(Grade $grade)
    {
        $grade->delete();
        return response()->json(null, 204);
    }
}