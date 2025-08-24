<?php

namespace App\Http\Controllers\API\params;

use App\Http\Controllers\Controller;
use App\Http\Resources\NameResource;
use App\Models\Direction;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;

class DirectionController extends Controller
{
    use HasQueryFilters;

    public function index(Request $request)
    {
        $query = Direction::query();
        $result = $this->applyQueryParameters($query, $request);
        return response()->json($result);
    }

    public function listNames()
    {
        // $directions = Direction::all();
        // return NameResource::collection($directions);
        return Direction::select('id','nom')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:directions,nom',
        ]);

        $direction = Direction::create($validated);
        return response()->json($direction, 201);
    }

    public function show(Direction $direction)
    {
        return response()->json($direction);
    }

    public function update(Request $request, Direction $direction)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:directions,nom,' . $direction->id,
        ]);

        $direction->update($validated);
        return response()->json($direction);
    }

    public function destroy(Direction $direction)
    {
        $direction->delete();
        return response()->json(null, 204);
    }
}