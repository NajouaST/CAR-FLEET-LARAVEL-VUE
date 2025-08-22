<?php

namespace App\Http\Controllers\API\parc;

use App\Http\Controllers\Controller;
use App\Http\Resources\MarqueResource;
use App\Http\Resources\NameResource;
use App\Models\Marque;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarqueController extends Controller
{
    use HasQueryFilters;
    public function listNames(Request $request)
    {
        $query = Marque::query();

        $result = $this->applyQueryParameters($query, $request);

        return collect($result['data'])
            ->map(fn($item) => (new MarqueResource($item))->additional(['mode' => 'minimal']));
    }

    // -----------------------
    // Index with filters
    // -----------------------
    public function index(Request $request)
    {
        $query = Marque::query();

        $result = $this->applyQueryParameters($query, $request);

        return [
            'code' => 200,
            'message' => 'Marques retrieved',
            'data' => collect($result['data'])
                ->map(fn($item) => (new MarqueResource($item))->additional(['mode' => 'grid'])),
            'totalRecords' => $result['totalRecords'],
        ];
    }

    // -----------------------
    // Store
    // -----------------------
    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('marques', 'public');
        }

        $marque = Marque::create($data);

        return [
            'code' => 201,
            'message' => 'Marque created successfully',
            'data' => new MarqueResource($marque),
        ];
    }

    // -----------------------
    // Show
    // -----------------------
    public function show(Marque $marque)
    {
        return new MarqueResource($marque);
    }

    // -----------------------
    // Update
    // -----------------------
    public function update(Request $request, Marque $marque)
    {
        $data = $this->validateRequest($request, $marque->id);

        if ($request->hasFile('image')) {
            if ($marque->image) {
                Storage::disk('public')->delete($marque->image);
            }
            $data['image'] = $request->file('image')->store('marques', 'public');
        }

        $marque->update($data);

        return [
            'code' => 200,
            'message' => 'Marque updated successfully',
            'data' => new MarqueResource($marque),
        ];
    }

    // -----------------------
    // Destroy
    // -----------------------
    public function destroy(Marque $marque)
    {
        if ($marque->image) {
            Storage::disk('public')->delete($marque->image);
        }

        $marque->delete();

        return [
            'code' => 200,
            'message' => 'Marque deleted successfully',
        ];
    }

    // -----------------------
    // Helpers
    // -----------------------
    private function validateRequest(Request $request, $marqueId = null)
    {
        return $request->validate([
            'name'  => 'required|string|max:255|unique:marques,name,' . $marqueId,
            'image' => 'nullable|image|max:2048',
        ]);
    }
}
