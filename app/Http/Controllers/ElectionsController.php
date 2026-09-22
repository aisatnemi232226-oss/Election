<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Election;
use App\Models\AcademicYear;

class ElectionsController extends Controller
{
    public function index()
    {
        $isArchived = request()->boolean('archived');
        $electionsQuery = Election::query()->orderBy('id', 'desc');

        if ($isArchived) {
            $electionsQuery->onlyTrashed();
        }

        $elections = $electionsQuery->get();
        $selectedElection = $elections->first();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('admin.elections', compact('elections', 'selectedElection', 'isArchived', 'academicYears'));
    }

    public function create()
    {
        return view('admin.elections.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'reg_start' => 'required|date|before_or_equal:reg_end',
            'reg_end' => 'required|date|after_or_equal:reg_start',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'candidates_group' => 'nullable|string|max:255',
            'candidates_result_announcements' => 'nullable|string',
        ]);

        $validated['admin_id'] = $request->user()->id;
        $election = new Election($validated);
        $election->deleted_at = null;
        $election->save();

        session()->flash('success', 'Election created successfully.');
        return redirect()->route('admin.elections.index');
    }

    public function restore($id)
    {
        $election = Election::onlyTrashed()->findOrFail($id);
        $election->restore();

        session()->flash('success', 'Election restored successfully.');
        return redirect()->route('admin.elections.index', ['archived' => 1]);
    }

    public function forceDestroy($id)
    {
        $election = Election::withTrashed()->findOrFail($id);
        $election->forceDelete();

        session()->flash('success', 'Election permanently deleted successfully.');
        return redirect()->route('admin.elections.index', ['archived' => 1]);
    }

    public function edit(Election $election)
    {
        return view('admin.elections.edit', compact('election'));
    }

    public function update(Request $request, Election $election)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'candidates_group' => 'nullable|string|max:255',
            'candidates_result_announcements' => 'nullable|string',
        ]);

        $election->update($request->all());

        session()->flash('success', 'Election updated successfully.');
        return redirect()->route('admin.elections.index');
    }

    public function destroy(Election $election)
    {
        $election->delete();

        session()->flash('success', 'Election deleted successfully.');
        return redirect()->route('admin.elections.index');
    }

    public function content()
    {
    $elections = Election::query()->orderBy('id', 'desc')->get();
    return view('admin.elections', compact('elections'));
    }
}
