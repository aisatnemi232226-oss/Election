<?php   

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VerifiedVoter;
use App\Models\User;
use App\Models\Acadyr;
use App\Models\Semesters;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VerifiedVotersExport;
use App\Imports\VerifiedVotersImport;
use Illuminate\Support\Facades\Log;

class VerifiedVoterController extends Controller
{
    public function index(Request $request)
    {
        \Log::info('Verified Voters Query Started');
    
        $perPage = (int) $request->input('perPage', 10);
        $page = (int) max(1, $request->input('page', 1));

        // Counts for pagination
        $baseQuery = User::query()->where('is_admin', false);
        $totalUsers = $baseQuery->count();
        $totalUnverified = (clone $baseQuery)->whereDoesntHave('verifiedVoter')->count();

        // Determine which users belong to this page: first consume unverified users, then verified
        $start = ($page - 1) * $perPage;
        $usersForPage = collect();

        if ($start < $totalUnverified) {
            $unverifiedSkip = $start;
            $unverifiedTake = min($perPage, $totalUnverified - $start);
            $unverifiedUsers = (clone $baseQuery)
                ->whereDoesntHave('verifiedVoter')
                ->orderByDesc('id')
                ->skip($unverifiedSkip)
                ->take($unverifiedTake)
                ->get();

            $usersForPage = $usersForPage->merge($unverifiedUsers);

            if ($unverifiedTake < $perPage) {
                $remaining = $perPage - $unverifiedTake;
                $verifiedUsers = (clone $baseQuery)
                    ->whereHas('verifiedVoter')
                    ->with(['verifiedVoter.acadyr', 'verifiedVoter.semesters'])
                    ->orderByDesc('id')
                    ->skip(0)
                    ->take($remaining)
                    ->get();

                $usersForPage = $usersForPage->merge($verifiedUsers);
            }
        } else {
            // Page is entirely within verified users
            $verifiedSkip = $start - $totalUnverified;
            $verifiedUsers = (clone $baseQuery)
                ->whereHas('verifiedVoter')
                ->with(['verifiedVoter.acadyr', 'verifiedVoter.semesters'])
                ->orderByDesc('id')
                ->skip($verifiedSkip)
                ->take($perPage)
                ->get();

            $usersForPage = $verifiedUsers;
        }

        $voters = new LengthAwarePaginator($usersForPage, $totalUsers, $perPage, $page, [
            'path' => url()->current(),
            'query' => $request->query(),
        ]);

        \Log::info('Verify Voters (users) Count: ' . $voters->total());
    
        // Check if it's an AJAX request (for filtering & pagination)
        if ($request->ajax()) {
            return response()->json([
                'voters' => collect($voters->items())->map(function ($user) {
                    $verified = $user->verifiedVoter;
                    return [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'idnumber' => $user->idnumber,
                        ],
                        'is_verified' => (bool) $verified,
                        'verified_voter_id' => $verified?->id,
                        'acadyr' => $verified?->acadyr ? [
                            'id' => $verified->acadyr->id,
                            'name' => $verified->acadyr->name,
                        ] : null,
                        'semesters' => $verified?->semesters ? [
                            'id' => $verified->semesters->id,
                            'name' => $verified->semesters->name,
                        ] : null,
                    ];
                })->values(),
                'pagination' => [
                    'total' => $voters->total(),
                    'per_page' => $voters->perPage(),
                    'current_page' => $voters->currentPage(),
                    'last_page' => $voters->lastPage(),
                    'from' => $voters->firstItem(),
                    'to' => $voters->lastItem(),
                ],
                'pagination_html' => ''
            ]);
        }

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

        $acadyrs = Acadyr::query()->orderByDesc('id')->get(['id', 'name']);
        $semesters = Semesters::query()->orderBy('id')->get(['id', 'name']);

        return view('admin.verified-voters', compact('voters', 'academicYearSemesters', 'perPage', 'acadyrs', 'semesters'));
    }    
    
    public function search(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $acadyrSemester = $request->input('acadyrSemester', '');
            $perPage = $request->input('perPage', 10);
            
            // Build base query with filters applied so counts and slicing are consistent
            $baseQuery = User::query()->where('is_admin', false);

            if (!empty($search)) {
                $baseQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('idnumber', 'like', "%{$search}%");
                });
            }

            if (!empty($acadyrSemester)) {
                [$acadyrId, $semId] = explode('-', $acadyrSemester);
                if ($acadyrId && $semId) {
                    $baseQuery->whereHas('verifiedVoter', function ($q) use ($acadyrId, $semId) {
                        $q->where('acadyr_id', $acadyrId)->where('sem_id', $semId);
                    });
                }
            }

            // Pagination parameters
            $perPage = ($perPage === 'all') ? (int) $baseQuery->count() : (int) $perPage;
            $page = (int) max(1, $request->input('page', 1));
            $start = ($page - 1) * $perPage;

            $totalUsers = $baseQuery->count();
            $totalUnverified = (clone $baseQuery)->whereDoesntHave('verifiedVoter')->count();

            $usersForPage = collect();

            if ($start < $totalUnverified) {
                $unverifiedSkip = $start;
                $unverifiedTake = min($perPage, $totalUnverified - $start);
                $unverifiedUsers = (clone $baseQuery)
                    ->whereDoesntHave('verifiedVoter')
                    ->orderByDesc('id')
                    ->skip($unverifiedSkip)
                    ->take($unverifiedTake)
                    ->get();

                $usersForPage = $usersForPage->merge($unverifiedUsers);

                if ($unverifiedTake < $perPage) {
                    $remaining = $perPage - $unverifiedTake;
                    $verifiedUsers = (clone $baseQuery)
                        ->whereHas('verifiedVoter')
                        ->with(['verifiedVoter.acadyr', 'verifiedVoter.semesters'])
                        ->orderByDesc('id')
                        ->skip(0)
                        ->take($remaining)
                        ->get();

                    $usersForPage = $usersForPage->merge($verifiedUsers);
                }
            } else {
                $verifiedSkip = $start - $totalUnverified;
                $verifiedUsers = (clone $baseQuery)
                    ->whereHas('verifiedVoter')
                    ->with(['verifiedVoter.acadyr', 'verifiedVoter.semesters'])
                    ->orderByDesc('id')
                    ->skip($verifiedSkip)
                    ->take($perPage)
                    ->get();

                $usersForPage = $verifiedUsers;
            }

            $users = new LengthAwarePaginator($usersForPage, $totalUsers, $perPage, $page, [
                'path' => url()->current(),
                'query' => $request->query(),
            ]);

            return response()->json([
                'voters' => collect($users->items())->map(function ($user) {
                    $verified = $user->verifiedVoter;
                    return [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'idnumber' => $user->idnumber,
                        ],
                        'is_verified' => (bool) $verified,
                        'verified_voter_id' => $verified?->id,
                        'acadyr' => $verified?->acadyr ? [
                            'id' => $verified->acadyr->id,
                            'name' => $verified->acadyr->name,
                        ] : null,
                        'semesters' => $verified?->semesters ? [
                            'id' => $verified->semesters->id,
                            'name' => $verified->semesters->name,
                        ] : null,
                    ];
                })->values(),
                'pagination' => [
                    'total' => $users->total(),
                    'per_page' => $users->perPage(),
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem()
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
            'acadyr_id' => 'required|exists:acadyr,id',
            'sem_id' => 'required|exists:semesters,id',
        ]);

        if (VerifiedVoter::where('users_id', $request->user_id)
            ->where('acadyr_id', $request->acadyr_id)
            ->where('sem_id', $request->sem_id)
            ->exists()) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already verified for this academic year and semester.'
                ], 422);
            }

            return redirect()->route('admin.verified-voters.index')
                ->with('error', 'User is already verified for this academic year and semester.');
        }

        VerifiedVoter::create([
            'users_id' => $request->user_id,
            'acadyr_id' => $request->acadyr_id,
            'sem_id' => $request->sem_id,
        ]);

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Voter verified successfully.'
            ]);
        }

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
