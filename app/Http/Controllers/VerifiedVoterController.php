<?php   

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VerifiedVoter;
use App\Models\User;
use App\Models\Acadyr;
use App\Models\Semesters;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VerifiedVotersExport;
use App\Imports\VerifiedVotersImport;
use Illuminate\Support\Facades\Log;

class VerifiedVoterController extends Controller
{
    public function index(Request $request)
    {
        \Log::info('Verified Voters Query Started');
    
        // Get filter and pagination parameters
        $academicYear = $request->input('academic_year');
        $perPage = $request->input('perPage', 10); // Default to 10 per page
        $perPage = $perPage === 'all' ? VerifiedVoter::count() : (int)$perPage;
    
        $query = VerifiedVoter::with(['users', 'acadyr', 'semesters']);
    
        // Apply academic year filter only if it's not "all"
        if (!empty($academicYear) && $academicYear !== "all") {
            $query->whereHas('acadyr', function ($q) use ($academicYear) {
                $q->where('name', $academicYear);
            });
        }
    
        // Always apply pagination
        $verifiedVoters = $query->paginate($perPage)->appends($request->except('page'));
    
        \Log::info('Verified Voters Count: ' . $verifiedVoters->total());
    
        // Check if it's an AJAX request (for filtering & pagination)
        if ($request->ajax()) {
            return response()->json([
                'verifiedVoters' => $verifiedVoters->items(),
                'pagination' => [
                    'total' => $verifiedVoters->total(),
                    'per_page' => $verifiedVoters->perPage(),
                    'current_page' => $verifiedVoters->currentPage(),
                    'last_page' => $verifiedVoters->lastPage(),
                    'from' => $verifiedVoters->firstItem(),
                    'to' => $verifiedVoters->lastItem(),
                ],
                'pagination_html' => view('partials.pagination', compact('verifiedVoters'))->render()
            ]);
        }
    
        // Fetch unique academic years and semesters
        $academicYearSemesters = VerifiedVoter::with(['acadyr', 'semesters'])   
            ->get()
            ->filter(fn($voter) => $voter->acadyr && $voter->semesters)
            ->map(fn($voter) => [
                'id' => $voter->acadyr->id . '-' . $voter->semesters->id,
                'name' => $voter->semesters->name . ' ' . $voter->acadyr->name,
                'semester_order' => $voter->semesters->id
            ])
            ->unique('id')
            ->sortBy('semester_order')
            ->values();
    
        return view('admin.verified-voters', compact('verifiedVoters', 'academicYearSemesters', 'perPage'));
    }    
    
    public function search(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $acadyrSemester = $request->input('acadyrSemester', '');
            $perPage = $request->input('perPage', 10);
            
            // Handle 'all' option for perPage
            if ($perPage === 'all') {
                $query = VerifiedVoter::with(['users', 'acadyr', 'semesters']);
                if (!empty($search)) {
                    $query->whereHas('users', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('idnumber', 'like', "%{$search}%");
                    });
                }
                if (!empty($acadyrSemester)) {
                    [$acadyrId, $semId] = explode('-', $acadyrSemester);
                    if ($acadyrId && $semId) {
                        $query->where('acadyr_id', $acadyrId)
                              ->where('sem_id', $semId);
                    }
                }
                $perPage = $query->count();
            }
    
            $query = VerifiedVoter::with(['users', 'acadyr', 'semesters']);
    
            if (!empty($search)) {
                $query->whereHas('users', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('idnumber', 'like', "%{$search}%");
                });
            }
    
            if (!empty($acadyrSemester)) {
                [$acadyrId, $semId] = explode('-', $acadyrSemester);
    
                if ($acadyrId && $semId) {
                    $query->where('acadyr_id', $acadyrId)
                          ->where('sem_id', $semId);
                }
            }
    
            $verifiedVoters = $query->paginate((int)$perPage);
            
            return response()->json([
                'voters' => $verifiedVoters->items(),
                'pagination' => [
                    'total' => $verifiedVoters->total(),
                    'per_page' => $verifiedVoters->perPage(),
                    'current_page' => $verifiedVoters->currentPage(),
                    'last_page' => $verifiedVoters->lastPage(),
                    'from' => $verifiedVoters->firstItem(),
                    'to' => $verifiedVoters->lastItem()
                ]
            ]);
    
        } catch (\Exception $e) {
            Log::error('Voter search error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while retrieving data'], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'acadyr_id' => 'required|exists:acadyrs,id',
            'sem_id' => 'required|exists:semesters,id',
        ]);

        if (VerifiedVoter::where('users_id', $request->user_id)
            ->where('acadyr_id', $request->acadyr_id)
            ->where('sem_id', $request->sem_id)
            ->exists()) {
            return redirect()->route('admin.verified-voters.index')
                ->with('error', 'User is already verified for this academic year and semester.');
        }

        VerifiedVoter::create([
            'users_id' => $request->user_id,
            'acadyr_id' => $request->acadyr_id,
            'sem_id' => $request->sem_id,
        ]);

        return redirect()->route('admin.verified-voters.index')->with('success', 'Voter verified successfully.');
    }

    public function destroy($id)
    {
        try {
            $voter = VerifiedVoter::findOrFail($id);
            $voter->delete();

            return response()->json(['success' => true, 'message' => 'Voter removed successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to remove voter.'], 500);
        }
    }

    public function export(Request $request)
    {
        $acadyrSemester = $request->input('semester_id');

        if (!$acadyrSemester || $acadyrSemester === "all") {
            $verifiedVoters = VerifiedVoter::with(['users', 'acadyr', 'semesters'])->get();
        } else {
            [$acadyrId, $semId] = explode('-', $acadyrSemester);

            $verifiedVoters = VerifiedVoter::with(['users', 'acadyr', 'semesters'])
                ->where('acadyr_id', $acadyrId)
                ->where('sem_id', $semId)
                ->get();
        }

        return Excel::download(new VerifiedVotersExport($verifiedVoters), 'verified_voters.xlsx');
    }

    public function import(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'importFile' => 'required|file|mimes:csv,xlsx,xls'
        ]);
    
        try {
            // Create an instance of the import class
            $import = new VerifiedVotersImport();
    
            // Log the file being uploaded for debugging
            Log::info('Starting voter import', [
                'filename' => $request->file('importFile')->getClientOriginalName(),
                'size' => $request->file('importFile')->getSize()
            ]);
    
            // Perform the import
            Excel::import($import, $request->file('importFile'));
    
            // Get the counts
            $successCount = $import->getSuccessCount();
            $duplicateCount = $import->getDuplicateCount();
            $failures = $import->getFailures();
    
            // Prepare the response message
            $message = "Import completed: {$successCount} voters imported successfully.";
            if ($duplicateCount > 0) {
                $message .= " {$duplicateCount} duplicate entries were skipped.";
            }
    
            if (!empty($failures)) {
                // Log failures
                Log::warning('Voter Import Failures', ['failures' => $failures]);
    
                // For AJAX requests, return JSON
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => $message,
                        'details' => [
                            'success_count' => $successCount,
                            'duplicate_count' => $duplicateCount,
                            'failures' => array_slice($failures, 0, 3)
                        ]
                    ]);
                }
    
                // For non-AJAX, redirect with detailed message
                return redirect()->back()
                    ->with('success', $message)
                    ->with('warning', 'Some entries had issues: ' . 
                        implode(', ', array_slice($failures, 0, 3)) . 
                        (count($failures) > 3 ? ' and ' . (count($failures) - 3) . ' more issues.' : '.'));
            }
    
            // For AJAX requests, return JSON success
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'details' => [
                        'success_count' => $successCount,
                        'duplicate_count' => $duplicateCount
                    ]
                ]);
            }
    
            // For non-AJAX, redirect with success message
            return redirect()->back()->with('success', $message);
    
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // Handle validation exceptions
            $failures = $e->failures();
            
            Log::error('Voter Import Validation Errors', [
                'failures' => collect($failures)->map(function($f) {
                    return [
                        'row' => $f->row(),
                        'attribute' => $f->attribute(),
                        'errors' => $f->errors()
                    ];
                })->toArray()
            ]);
    
            // Create user-friendly error message
            $errorMsg = 'Import validation failed: ' . 
                collect($failures)->take(3)->map(function ($failure) {
                    return 'Row ' . $failure->row() . ' (' . $failure->attribute() . '): ' . 
                        implode(', ', $failure->errors());
                })->implode(' | ');
                
            if (count($failures) > 3) {
                $errorMsg .= ' and ' . (count($failures) - 3) . ' more errors';
            }
    
            // For AJAX requests, return JSON error
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMsg,
                    'failures' => collect($failures)->take(10)->toArray()
                ]);
            }
    
            // For non-AJAX, redirect with error
            return redirect()->back()->with('error', $errorMsg);
    
        } catch (\Exception $e) {
            // Handle any other unexpected errors
            Log::error('Voter Import Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            // For AJAX requests, return JSON error
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An unexpected error occurred during import: ' . $e->getMessage()
                ], 500);
            }
    
            // For non-AJAX, redirect with error
            return redirect()->back()->with('error', 'An unexpected error occurred during import: ' . $e->getMessage());
        }
    }
    
    
    public function downloadSample()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sample_voters.csv"'
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'ID Number', 'Academic Year', 'Semester']);
            fputcsv($file, ['John Doe', '123456', '2024-2025', '1st Semester']);
            fputcsv($file, ['Jane Smith', '789012', '2024-2025', '2nd Semester']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
