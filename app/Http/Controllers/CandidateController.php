<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\User;
use App\Models\Course;
use App\Models\VerifiedVoter;
use App\Models\ElectionParty;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CandidateController extends Controller
{
    /**
     * Display a listing of the candidates.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try {
            Log::info('CandidateController@index - Starting');
            
            // Simple query to check if the database connection works
            $candidateCount = DB::table('candidate')->count();
            Log::info('Raw candidate count: ' . $candidateCount);
            
            $perPage = 10;
            $candidates = Candidate::orderBy('created_at', 'desc')->paginate($perPage);
            
            // Get courses and parties separately to avoid relationship issues
            $courses = Course::all();
            $parties = ElectionParty::all();
            
            Log::info('CandidateController@index - Success');
            return view('admin.candidates', compact('candidates', 'courses', 'parties'));
        } catch (\Exception $e) {
            Log::error('Error in candidates index: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Failed to load candidates page: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created candidate in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
  public function store(Request $request)
{
    Log::info('Store method triggered', $request->all());
    
    try {
        // Auto-generate ID number if not provided
        if (empty($request->id_number)) {
            do {
                $generatedId = 'ST' . str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);
            } while (Candidate::where('id_number', $generatedId)->exists());
            
            $request->merge(['id_number' => $generatedId]);
            Log::info('Auto-generated ID number: ' . $generatedId);
        }

        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'profile_img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner_img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'required|string',
            'course_id' => 'required|exists:courses,id',
            'party_id' => 'nullable|exists:electionparty,id',
            'platforms' => 'required|string',
            'status' => 'required|in:Active,Inactive',
            'id_number' => 'required|string|max:255|unique:candidate,id_number',
        ]);

        // Verify course exists
        $course = Course::find($request->course_id);
        if (!$course) {
            return response()->json([
                'success' => false, 
                'message' => 'Course not found. Please select a valid course.'
            ], 422);
        }

        // Verify party exists if provided
        $party = null;
        if ($request->filled('party_id')) {
            $party = ElectionParty::find($request->party_id);
            if (!$party) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Party not found. Please select a valid party.'
                ], 422);
            }
        }

        // Create new candidate instance
        $candidate = new Candidate();
        $candidate->name = $request->name;
        $candidate->description = $request->description;
        $candidate->course_id = $course->id;
        $candidate->electionparty_id = $party ? $party->id : null;
        $candidate->platforms = $request->platforms;
        $candidate->status = $request->status;
        $candidate->id_number = $request->id_number;

        // Handle profile image upload
        if ($request->hasFile('profile_img')) {
            $profileFile = $request->file('profile_img');
            $profileFilename = time() . '_' . Str::random(10) . '_profile.' . $profileFile->getClientOriginalExtension();
            
            // Store the file
            $profileFile->storeAs('upload/profile-picture', $profileFilename, 'public');
            $candidate->profile_img = $profileFilename;
            
            Log::info('Profile image uploaded: ' . $profileFilename);
        }

        // Handle banner image upload
        if ($request->hasFile('banner_img')) {
            $bannerFile = $request->file('banner_img');
            $bannerFilename = time() . '_' . Str::random(10) . '_banner.' . $bannerFile->getClientOriginalExtension();
            
            // Store the file
            $bannerFile->storeAs('upload/banner', $bannerFilename, 'public');
            $candidate->banner_img = $bannerFilename;
            
            Log::info('Banner image uploaded: ' . $bannerFilename);
        }

        // Save the candidate
        $candidate->save();

        Log::info('Candidate created successfully with ID: ' . $candidate->id);

        return response()->json([
            'success' => true, 
            'message' => 'Candidate created successfully.',
            'candidate' => $candidate
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation error creating candidate: ' . json_encode($e->errors()));
        return response()->json([
            'success' => false, 
            'message' => 'Validation failed.',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error('Error creating candidate: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false, 
            'message' => 'Error creating candidate: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Show candidate details.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $candidate = Candidate::findOrFail($id);
            return response()->json($candidate);
        } catch (\Exception $e) {
            Log::error('Error fetching candidate: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Candidate not found'], 404);
        }
    }

    /**
     * Update the specified candidate.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
public function update(Request $request, $id)
{
    Log::info('Update method triggered', $request->all());
    
    try {
        // Find the existing candidate first
        $candidate = Candidate::findOrFail($id);
        
        // Auto-generate ID number if not provided and candidate doesn't have one
        if (empty($request->id_number) && empty($candidate->id_number)) {
            do {
                $generatedId = 'ST' . str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);
            } while (Candidate::where('id_number', $generatedId)->exists());
            
            $request->merge(['id_number' => $generatedId]);
            Log::info('Auto-generated ID number for update: ' . $generatedId);
        } elseif (empty($request->id_number) && !empty($candidate->id_number)) {
            // If no id_number in request but candidate already has one, keep the existing one
            $request->merge(['id_number' => $candidate->id_number]);
            Log::info('Using existing ID number: ' . $candidate->id_number);
        }

        $request->validate([
            'profile_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'id_number' => 'required|string|max:255|unique:candidate,id_number,' . $id,
            'course_id' => 'required|exists:courses,id',
            'party_id' => 'nullable|exists:electionparty,id',
            'status' => 'required|string',
            'platforms' => 'required|string',
            'description' => 'required|string',
        ]);
        
        // Verify course exists
        if ($request->has('course_id')) {
            $course = Course::find($request->course_id);
            if (!$course) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Course not found. Please select a valid course.'
                ]);
            }
            $candidate->course_id = $course->id;
        }
        
        // Verify party exists if provided
        if ($request->has('party_id') && !empty($request->party_id)) {
            $party = ElectionParty::find($request->party_id);
            if ($party) {
                $candidate->electionparty_id = $party->id;
            }
        } else {
            $candidate->electionparty_id = null;
        }
        
        // Handle profile image
        if ($request->hasFile('profile_img')) {
            if ($candidate->profile_img && Storage::disk('public')->exists('upload/profile-picture/' . $candidate->profile_img)) {
                Storage::disk('public')->delete('upload/profile-picture/' . $candidate->profile_img);
            }
            
            $profileFile = $request->file('profile_img');
            $profileFilename = time() . '_profile.' . $profileFile->getClientOriginalExtension();
            $profileFile->storeAs('upload/profile-picture', $profileFilename, 'public');
            $candidate->profile_img = $profileFilename;
        }
        
        // Handle banner image
        if ($request->hasFile('banner_img')) {
            if ($candidate->banner_img && Storage::disk('public')->exists('upload/banner/' . $candidate->banner_img)) {
                Storage::disk('public')->delete('upload/banner/' . $candidate->banner_img);
            }
            
            $bannerFile = $request->file('banner_img');
            $bannerFilename = time() . '_banner.' . $bannerFile->getClientOriginalExtension();
            $bannerFile->storeAs('upload/banner', $bannerFilename, 'public');
            $candidate->banner_img = $bannerFilename;
        }
        
        // Update other fields
        if ($request->has('name')) $candidate->name = $request->name;
        if ($request->has('id_number')) $candidate->id_number = $request->id_number;
        if ($request->has('status')) $candidate->status = $request->status;
        if ($request->has('platforms')) $candidate->platforms = $request->platforms;
        if ($request->has('description')) $candidate->description = $request->description;
        
        $candidate->save();
        
        return response()->json(['success' => true, 'message' => 'Candidate updated successfully.']);
    } catch (\Exception $e) {
        Log::error('Error updating candidate: ' . $e->getMessage());
        return response()->json([
            'success' => false, 
            'message' => 'Error updating candidate: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Remove the specified candidate from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Log::info('Destroy method triggered for candidate ID: ' . $id);
        
        try {
            $candidate = Candidate::findOrFail($id);
            
            // Delete associated images
            if ($candidate->profile_img && Storage::disk('public')->exists('upload/profile-picture/' . $candidate->profile_img)) {
                Storage::disk('public')->delete('upload/profile-picture/' . $candidate->profile_img);
                Log::info('Deleted profile image: ' . $candidate->profile_img);
            }
            
            if ($candidate->banner_img && Storage::disk('public')->exists('upload/banner/' . $candidate->banner_img)) {
                Storage::disk('public')->delete('upload/banner/' . $candidate->banner_img);
                Log::info('Deleted banner image: ' . $candidate->banner_img);
            }
            
            // Delete the candidate record
            $candidate->delete();
            
            Log::info('Candidate deleted successfully with ID: ' . $id);
            
            return response()->json([
                'success' => true, 
                'message' => 'Candidate deleted successfully.'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Candidate not found for deletion: ' . $id);
            return response()->json([
                'success' => false, 
                'message' => 'Candidate not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting candidate: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false, 
                'message' => 'Error deleting candidate: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get paginated candidates with optional filtering.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function paginateCandidates(Request $request)
    {
        try {
            Log::info('paginateCandidates called with params: ', $request->all());
            $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
            
            // Get authenticated user info safely
            $currentUser = 'guest';
            if (auth()->check()) {
                $user = auth()->user();
                $currentUser = $user ? ($user->username ?? $user->email ?? 'authenticated_user') : 'guest';
            }
            
            // Build the base query with course join for search
            $query = Candidate::leftJoin('courses', 'candidate.course_id', '=', 'courses.id')
                             ->leftJoin('electionparty', 'candidate.electionparty_id', '=', 'electionparty.id')
                             ->select('candidate.*');
            
            // Apply search filter if provided - now includes course names
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                Log::info('Applying search filter: ' . $searchTerm);
                
                $query->where(function($q) use ($searchTerm) {
                    $q->where('candidate.name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('candidate.status', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('courses.name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('electionparty.name', 'LIKE', "%{$searchTerm}%");
                });
            }
            
            // Apply status filter if provided
            if ($request->filled('status')) {
                Log::info('Applying status filter: ' . $request->status);
                $query->where('candidate.status', $request->status);
            }
            
            // Apply course filter if provided
            if ($request->filled('course')) {
                Log::info('Applying course filter: ' . $request->course);
                $query->where('candidate.course_id', $request->course);
            }
            
            // Execute the paginated query
            Log::info('Executing paginated query...');
            $candidates = $query->orderBy('candidate.created_at', 'desc')->paginate(10);
            Log::info('Basic pagination successful, count: ' . $candidates->count());
            
            // Manually load related data to avoid relationship issues
            $candidatesData = [];
            foreach ($candidates as $candidate) {
                $candidateArray = $candidate->toArray();
                
                // Get course data
                if ($candidate->course_id) {
                    $course = Course::find($candidate->course_id);
                    $candidateArray['course'] = $course ? $course->toArray() : null;
                } else {
                    $candidateArray['course'] = null;
                }
                
                // Get party data
                if ($candidate->electionparty_id) {
                    $party = ElectionParty::find($candidate->electionparty_id);
                    $candidateArray['party'] = $party ? $party->toArray() : null;
                } else {
                    $candidateArray['party'] = null;
                }
                
                $candidatesData[] = $candidateArray;
            }
            
            // Generate pagination HTML with appends to maintain search parameters
            $candidates->appends($request->query());
            $paginationHtml = $candidates->links()->render();
            
            Log::info('paginateCandidates successful');
            return response()->json([
                'success' => true,
                'data' => $candidatesData,
                'pagination_html' => $paginationHtml,
                'metadata' => [
                    'timestamp' => $currentDateTime,
                    'user' => $currentUser,
                    'total' => $candidates->total(),
                    'page' => $candidates->currentPage(),
                    'last_page' => $candidates->lastPage(),
                    'showing_from' => $candidates->firstItem(),
                    'showing_to' => $candidates->lastItem()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in paginateCandidates: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('Request data: ' . json_encode($request->all()));
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load candidates: ' . $e->getMessage(),
                'debug' => [
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ]
            ], 500);
        }
    }

    /**
     * Test database queries for debugging.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function testQuery()
    {
        try {
            Log::info('Testing database queries...');
            
            // Test basic database connectivity
            $tables = DB::select("SHOW TABLES");
            Log::info('Database connected, tables found: ' . count($tables));
            
            // Test candidate table
            $candidateCount = 0;
            $modelCount = 0;
            $candidateTableExists = false;
            $candidateSample = [];
            
            try {
                $tables = DB::select("SHOW TABLES LIKE 'candidate'");
                $candidateTableExists = !empty($tables);
                
                if ($candidateTableExists) {
                    $candidateCount = DB::table('candidate')->count();
                    Log::info('Raw candidate count: ' . $candidateCount);
                    
                    // Test model
                    $modelCount = Candidate::count();
                    Log::info('Model candidate count: ' . $modelCount);
                    
                    // Sample data
                    $candidateSample = DB::table('candidate')->select('id', 'name', 'course_id', 'electionparty_id')
                        ->limit(3)->get();
                }
            } catch (\Exception $e) {
                Log::warning('Candidate table test failed: ' . $e->getMessage());
            }
            
            // Test course table
            $courseCount = 0;
            $courseTableExists = false;
            $courseSample = [];
            
            try {
                $tables = DB::select("SHOW TABLES LIKE 'courses'");
                $courseTableExists = !empty($tables);
                
                if ($courseTableExists) {
                    $courseCount = Course::count();
                    Log::info('Course count: ' . $courseCount);
                    $courseSample = DB::table('courses')->select('id', 'name')->limit(3)->get();
                }
            } catch (\Exception $e) {
                Log::warning('Course table test failed: ' . $e->getMessage());
            }
            
            // Test party table
            $partyCount = 0;
            $partyTableExists = false;
            $partySample = [];
            
            try {
                $tables = DB::select("SHOW TABLES LIKE 'electionparty'");
                $partyTableExists = !empty($tables);
                
                if ($partyTableExists) {
                    $partyCount = ElectionParty::count();
                    Log::info('Party count: ' . $partyCount);
                    $partySample = DB::table('electionparty')->select('id', 'name')->limit(3)->get();
                }
            } catch (\Exception $e) {
                Log::warning('Party table test failed: ' . $e->getMessage());
            }
            
            // Test relationships if all tables exist
            $relationshipTest = [];
            if ($candidateTableExists && $courseTableExists && $partyTableExists) {
                try {
                    $candidate = Candidate::first();
                    if ($candidate) {
                        $courseId = $candidate->course_id;
                        $partyId = $candidate->electionparty_id;
                        
                        $course = Course::find($courseId);
                        $party = ElectionParty::find($partyId);
                        
                        $relationshipTest = [
                            'candidate_id' => $candidate->id,
                            'candidate_name' => $candidate->name,
                            'course_id' => $courseId,
                            'course_found' => !!$course,
                            'course_name' => $course ? $course->name : null,
                            'party_id' => $partyId,
                            'party_found' => !!$party,
                            'party_name' => $party ? $party->name : null
                        ];
                    }
                } catch (\Exception $e) {
                    Log::warning('Relationship test failed: ' . $e->getMessage());
                    $relationshipTest = ['error' => $e->getMessage()];
                }
            }
            
            return response()->json([
                'success' => true,
                'database_connected' => true,
                'tables' => [
                    'candidate' => [
                        'exists' => $candidateTableExists,
                        'count' => $candidateCount,
                        'model_count' => $modelCount,
                        'sample' => $candidateSample
                    ],
                    'courses' => [
                        'exists' => $courseTableExists,
                        'count' => $courseCount,
                        'sample' => $courseSample
                    ],
                    'electionparty' => [
                        'exists' => $partyTableExists,
                        'count' => $partyCount,
                        'sample' => $partySample
                    ]
                ],
                'relationship_test' => $relationshipTest
            ]);
        } catch (\Exception $e) {
            Log::error('Test query failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'database_connected' => false,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    /**
     * Get candidate details in JSON format.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showJson($id)
    {
        try {
            $candidate = Candidate::findOrFail($id);
            $candidateData = $candidate->toArray();
            
            // Load course data
            if ($candidate->course_id) {
                $course = Course::find($candidate->course_id);
                $candidateData['course'] = $course ? $course->toArray() : null;
            }
            
            // Load party data
            if ($candidate->electionparty_id) {
                $party = ElectionParty::find($candidate->electionparty_id);
                $candidateData['party'] = $party ? $party->toArray() : null;
            }
            
            return response()->json($candidateData);
        } catch (\Exception $e) {
            Log::error('Error fetching candidate: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Candidate not found'], 404);
        }
    }
}