<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Position;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PositionController extends Controller
{
    public function index()
    {
        $isArchived = request()->boolean('archived');
        $positionsQuery = Position::query()->orderBy('id', 'desc');
        if ($isArchived) {
            $positionsQuery->onlyTrashed();
        }
        $positions = $positionsQuery->paginate(10)->appends(['archived' => $isArchived ? 1 : 0]);

        return view('admin.positions', compact('positions', 'isArchived'));
    }

    public function paginatePositions(Request $request)
    {
        $isArchived = $request->boolean('archived');
        $positions = Position::query()->orderBy('id', 'desc');
        if ($isArchived) {
            $positions->onlyTrashed();
        }
        
        // Handle search
        if ($request->has('search')) {
            $search = $request->get('search');
            $positions = $positions->where(function($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $positions = $positions->paginate(10);

        $positions->appends($request->query());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $positions->items(),
                'pagination_html' => view('components.pagination', ['paginator' => $positions])->render()
            ]);
        }

        return view('admin.positions', compact('positions', 'isArchived'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:positions',
            'description' => 'required|string',
            'tile_img' => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:5120',
        ]);

        // Add default status if not provided
        $validated['status'] = $validated['status'] ?? 'active';

        if ($request->hasFile('tile_img')) {
            $file = $request->file('tile_img');
            $name = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('upload/position-tiles', $file, $name);
            $validated['tile_img'] = 'position-tiles/' . $name;
        }

        Position::create($validated);

        return redirect()->back()->with('success', 'Position created successfully');
    }

    // This method signature must match the resource route expectation
    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:positions,name,' . $position->id,
            'description' => 'required|string',
            'tile_img' => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:5120',
        ]);

        if ($request->hasFile('tile_img')) {
            $file = $request->file('tile_img');
            $name = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('upload/position-tiles', $file, $name);
            $validated['tile_img'] = 'position-tiles/' . $name;

            if (!empty($position->tile_img) && Storage::disk('public')->exists('upload/' . $position->tile_img)) {
                Storage::disk('public')->delete('upload/' . $position->tile_img);
            }
        }

        $position->update($validated);

        return redirect()->back()->with('success', 'Position updated successfully');
    }

    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        $position->delete();

        return redirect()->back()->with('success', 'Position moved to trash');
    }

    public function restore($id)
    {
        $position = Position::onlyTrashed()->findOrFail($id);
        $position->restore();

        return redirect()->route('admin.positions.index', ['archived' => 1])->with('success', 'Position restored successfully');
    }

    public function forceDestroy($id)
    {
        $position = Position::withTrashed()->findOrFail($id);

        if (!empty($position->tile_img) && Storage::disk('public')->exists('upload/' . $position->tile_img)) {
            Storage::disk('public')->delete('upload/' . $position->tile_img);
        }

        $position->forceDelete();

        return redirect()->route('admin.positions.index', ['archived' => 1])->with('success', 'Position permanently deleted');
    }
}