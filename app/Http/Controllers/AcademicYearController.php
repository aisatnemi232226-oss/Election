<?php

namespace App\Http\Controllers;

use App\Models\Acadyr;
use App\Models\Semesters;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AcademicYearController extends Controller
{
    public function index(Request $request): View
    {
        $query = Acadyr::orderBy('created_at', 'desc');

        if ($request->has('filter') && $request->filter) {
            $filter = $request->filter;
            $query->where('name', 'like', "%{$filter}%");
        }

        $academicYears = $query->get();
        $semesters = Semesters::all();

        $combinedData = [];
        foreach ($academicYears as $year) {
            foreach ($semesters as $semester) {
                $combinedData[] = (object) [
                    'id' => $year->id . '_' . $semester->id,
                    'academic_year_id' => $year->id,
                    'semester_id' => $semester->id,
                    'formatted_name' => $semester->name . ' ' . $year->name,
                    'status' => $year->status,
                    'is_active' => $year->is_active,
                ];
            }
        }

        return view('admin.academic-year', compact('combinedData'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        Acadyr::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.academic-year.index')
            ->with('success', 'Academic year created successfully.');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $academicYear = Acadyr::findOrFail($id);
        $academicYear->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.academic-year.index')
            ->with('success', 'Academic year updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $academicYear = Acadyr::findOrFail($id);
        $academicYear->delete();

        return redirect()->route('admin.academic-year.index')
            ->with('success', 'Academic year deleted successfully.');
    }

    public function toggleStatus(Request $request): RedirectResponse
    {
        $request->validate([
            'academic_year_ids' => 'required|array',
            'academic_year_ids.*' => 'exists:acadyr,id',
        ]);

        foreach ($request->academic_year_ids as $id) {
            $academicYear = Acadyr::findOrFail($id);
            $academicYear->update([
                'status' => $academicYear->status === 'active' ? 'inactive' : 'active'
            ]);
        }

        return redirect()->route('admin.academic-year.index')
            ->with('success', 'Academic year status updated successfully.');
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $request->validate([
            'academic_year_ids' => 'required|array',
            'academic_year_ids.*' => 'exists:acadyr,id',
        ]);

        Acadyr::whereIn('id', $request->academic_year_ids)->delete();

        return redirect()->route('admin.academic-year.index')
            ->with('success', 'Selected academic years deleted successfully.');
    }
}