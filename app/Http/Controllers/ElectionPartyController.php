<?php

namespace App\Http\Controllers;

use App\Models\ElectionParty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ElectionPartyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $isArchived = $request->boolean('archived');
            $query = ElectionParty::query();

            if ($isArchived) {
                $query->onlyTrashed();
            }

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

            return view('admin.election-parties', compact('electionParties', 'isArchived'));
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
            if (!Schema::hasColumn('electionparty', 'logo')) {
                return back()->with('error', 'Party logo cannot be saved because the database is missing the logo column. Please run: php artisan migrate')
                    ->withInput();
            }

            if ($request->has('logo') && !$request->hasFile('logo')) {
                return back()->with('error', 'Logo upload failed. Please try a smaller image (max 2MB) or check your server PHP settings: upload_max_filesize and post_max_size.')
                    ->withInput();
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:electionparty,name',
                'description' => 'nullable|string',
                'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
                'status' => 'nullable|string|max:255',
            ]);

            if (empty($validated['status'])) {
                $validated['status'] = 'Active';
            }

            if ($request->hasFile('logo')) {
                $validated['logo'] = $request->file('logo')->store('election-parties', 'public');

                if (!empty($validated['logo']) && !Storage::disk('public')->exists($validated['logo'])) {
                    return back()->with('error', 'Logo was uploaded but could not be saved on disk. Please check storage permissions and run: php artisan storage:link')
                        ->withInput();
                }
            }

            $party = ElectionParty::create($validated);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Election party created successfully.',
                    'party' => $party,
                ], 201);
            }

            return redirect()->route('admin.election-parties.index')
                ->with('success', 'Election party created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating election party: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create election party. Please try again.',
                ], 500);
            }

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

            if (!Schema::hasColumn('electionparty', 'logo')) {
                return back()->with('error', 'Party logo cannot be saved because the database is missing the logo column. Please run: php artisan migrate')
                    ->withInput();
            }

            if ($request->has('logo') && !$request->hasFile('logo')) {
                return back()->with('error', 'Logo upload failed. Please try a smaller image (max 2MB) or check your server PHP settings: upload_max_filesize and post_max_size.')
                    ->withInput();
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:electionparty,name,' . $id,
                'description' => 'nullable|string',
                'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
                'status' => 'nullable|string|max:255',
            ]);

            if (!array_key_exists('status', $validated) || $validated['status'] === null || $validated['status'] === '') {
                $validated['status'] = $electionParty->status ?: 'Active';
            }

            if ($request->hasFile('logo')) {
                if (!empty($electionParty->logo) && Storage::disk('public')->exists($electionParty->logo)) {
                    Storage::disk('public')->delete($electionParty->logo);
                }
                $validated['logo'] = $request->file('logo')->store('election-parties', 'public');

                if (!empty($validated['logo']) && !Storage::disk('public')->exists($validated['logo'])) {
                    return back()->with('error', 'Logo was uploaded but could not be saved on disk. Please check storage permissions and run: php artisan storage:link')
                        ->withInput();
                }
            }

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
                           ->with('success', 'Election party moved to trash.');
        } catch (\Exception $e) {
            Log::error('Error deleting election party: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete election party. Please try again.');
        }
    }

    public function restore($id)
    {
        try {
            $party = ElectionParty::onlyTrashed()->findOrFail($id);
            $party->restore();

            return redirect()->route('admin.election-parties.index', ['archived' => 1])
                ->with('success', 'Election party restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring election party: ' . $e->getMessage());
            return back()->with('error', 'Failed to restore election party. Please try again.');
        }
    }

    public function forceDestroy($id)
    {
        try {
            $party = ElectionParty::withTrashed()->findOrFail($id);

            if (!empty($party->logo) && Storage::disk('public')->exists($party->logo)) {
                Storage::disk('public')->delete($party->logo);
            }

            $party->forceDelete();

            return redirect()->route('admin.election-parties.index', ['archived' => 1])
                ->with('success', 'Election party permanently deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error permanently deleting election party: ' . $e->getMessage());
            return back()->with('error', 'Failed to permanently delete election party. Please try again.');
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

            $rows = ElectionParty::whereIn('id', $request->party_ids)->get();
            ElectionParty::whereIn('id', $request->party_ids)->delete();

            $admin = auth()->user();
            AuditLogger::log(
                $request,
                $admin ? $admin->id : null,
                'AUDIT',
                'DELETE',
                'Election Party Bulk Archived',
                'Archived ' . count($request->party_ids) . ' election parties.',
                'electionparty',
                null,
                ['ids' => $request->party_ids, 'rows' => $rows->toArray()],
                null
            );

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