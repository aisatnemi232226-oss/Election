<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    /**
     * Display a listing of the reports.
     */
    public function index(Request $request)
    {
        $query = Report::query();

        // Handle search/filter
        if ($request->has('filter') && $request->filter) {
            $query->filter($request->filter);
        }

        $reports = $query->orderBy('created_at', 'desc')->get();

        return view('admin.reports', compact('reports'));
    }

    /**
     * Store a newly created report in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|in:Voting statistics,Candidate performance,Election summary,User participation,System logs',
        ]);

        Report::create([
            'name' => $request->name,
            'description' => $request->description ?? '',
            'datalisted' => 'pending',
            'type' => $request->type ?? 'System logs',
            'admin_id' => Auth::id(),
        ]);

        return redirect()->route('admin.reports.index')->with('success', 'Report created successfully.');
    }

    /**
     * Update the specified report in storage.
     */
    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|string',
        ]);

        $report->update([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type ?? $report->type,
        ]);

        return redirect()->route('admin.reports.index')->with('success', 'Report updated successfully.');
    }

    /**
     * Remove the specified reports from storage.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'report_ids' => 'required|array',
            'report_ids.*' => 'exists:reports,id'
        ]);

        Report::whereIn('id', $request->report_ids)->delete();

        return redirect()->route('admin.reports.index')->with('success', 'Selected reports deleted successfully.');
    }

    /**
     * Export reports to CSV
     */
    public function export()
    {
        $reports = Report::all();
        
        $filename = 'reports_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($reports) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Report Name', 'Date', 'Status', 'Description']);

            foreach ($reports as $report) {
                fputcsv($file, [
                    $report->name,
                    $report->created_at->format('Y-m-d'),
                    $report->status === 'completed' ? 'Completed' : 'Pending',
                    $report->description
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}