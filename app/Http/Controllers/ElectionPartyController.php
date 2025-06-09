<?php

namespace App\Http\Controllers;

use App\Models\ElectionParty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ElectionPartyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = ElectionParty::query();

            // Apply search filter if provided
            if ($request->has('filter') && !empty($request->filter)) {
                $filter = $request->filter;
                $query->where(function($q) use ($filter) {
                    $q->where('name', 'LIKE', "%{$filter}%")
                      ->orWhere('status', 'LIKE', "%{$filter}%");
                });
            }

            // Get all election parties
            $electionParties = $query->orderBy('name', 'asc')->get();

            return view('admin.election-parties', compact('electionParties'));
        } catch (\Exception $e) {
            Log::error('Error fetching election parties: ' . $e->getMessage());
            return back()->with('error', 'Failed to load election parties. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.election-parties.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:electionparty,name',
                'status' => 'required|string|max:255',
            ]);

            ElectionParty::create($validated);

            return redirect()->route('admin.election-parties.index')
                           ->with('success', 'Election party created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating election party: ' . $e->getMessage());
            return back()->with('error', 'Failed to create election party. Please try again.')
                        ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $electionParty = ElectionParty::findOrFail($id);
            return response()->json($electionParty);
        } catch (\Exception $e) {
            Log::error('Error fetching election party: ' . $e->getMessage());
            return response()->json(['error' => 'Election party not found'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $electionParty = ElectionParty::findOrFail($id);
            return view('admin.election-parties.edit', compact('electionParty'));
        } catch (\Exception $e) {
            Log::error('Error fetching election party for edit: ' . $e->getMessage());
            return back()->with('error', 'Election party not found.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $electionParty = ElectionParty::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:electionparty,name,' . $id,
                'status' => 'required|string|max:255',
            ]);

            $electionParty->update($validated);

            return redirect()->route('admin.election-parties.index')
                           ->with('success', 'Election party updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating election party: ' . $e->getMessage());
            return back()->with('error', 'Failed to update election party. Please try again.')
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $electionParty = ElectionParty::findOrFail($id);
            $electionParty->delete();

            return redirect()->route('admin.election-parties.index')
                           ->with('success', 'Election party deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting election party: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete election party. Please try again.');
        }
    }

    /**
     * Handle bulk delete operations.
     */
    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'party_ids' => 'required|array',
                'party_ids.*' => 'exists:electionparty,id'
            ]);

            ElectionParty::whereIn('id', $request->party_ids)->delete();

            return redirect()->route('admin.election-parties.index')
                           ->with('success', 'Selected election parties deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error bulk deleting election parties: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete selected election parties. Please try again.');
        }
    }

    /**
     * Get election party data for AJAX requests.
     */
    public function getPartyData($id)
    {
        try {
            $electionParty = ElectionParty::findOrFail($id);
            return response()->json($electionParty);
        } catch (\Exception $e) {
            Log::error('Error fetching election party data: ' . $e->getMessage());
            return response()->json(['error' => 'Election party not found'], 404);
        }
    }
}