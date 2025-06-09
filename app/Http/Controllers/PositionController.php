<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Position;

class PositionController extends Controller
{
    public function index()
    {
        // Include soft deleted records so all positions show up
        $positions = Position::withTrashed()->orderBy('id', 'desc')->paginate(10);
        return view('admin.positions', compact('positions'));
    }

    public function paginatePositions(Request $request)
    {
        // Include soft deleted records
        $positions = Position::withTrashed()->orderBy('id', 'desc');
        
        // Handle search
        if ($request->has('search')) {
            $search = $request->get('search');
            $positions = $positions->where(function($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $positions = $positions->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $positions->items(),
                'pagination_html' => view('components.pagination', ['paginator' => $positions])->render()
            ]);
        }

        return view('admin.positions', compact('positions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:positions',
            'description' => 'required|string'
        ]);

        // Add default status if not provided
        $validated['status'] = $validated['status'] ?? 'active';

        Position::create($validated);

        return redirect()->back()->with('success', 'Position created successfully');
    }

    // This method signature must match the resource route expectation
    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:positions,name,' . $position->id,
            'description' => 'required|string'
        ]);

        $position->update($validated);

        return redirect()->back()->with('success', 'Position updated successfully');
    }

   public function destroy($id)
{
    // Find the position including soft deleted ones
    $position = Position::withTrashed()->findOrFail($id);
    
    if ($position->trashed()) {
        // If already soft deleted, force delete it permanently
        $position->forceDelete();
        return redirect()->back()->with('success', 'Position permanently deleted');
    } else {
        // If not deleted, soft delete it
        $position->delete();
        return redirect()->back()->with('success', 'Position moved to trash');
    }
}

    public function restore($id)
    {
        $position = Position::withTrashed()->findOrFail($id);
        $position->restore();

        return redirect()->back()->with('success', 'Position restored successfully');
    }

    public function forceDelete($id)
    {
        $position = Position::withTrashed()->findOrFail($id);
        $position->forceDelete();

        return redirect()->back()->with('success', 'Position permanently deleted');
    }
}