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
            
            $perPage = 10;
            $candidates = Candidate::with(['course', 'party'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
            
            // Get courses and parties for dropdowns
            $courses = Course::orderBy('name')->get();
            $parties = ElectionParty::orderBy('name')->get();
            
            Log::info('CandidateController@index - Success, loaded ' . $candidates->count() . ' candidates');
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
        Log::info('Store method triggered', $request->except(['profile_img', 'banner_img']));
        
        try {
            // Validate the request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'profile_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'banner_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'required|string',
                'course_id' => 'required|exists:courses,id',
                'party_id' => 'nullable|exists:electionparty,id',
                'platforms' => 'required|string',
                'status' => 'required|in:Active,Inactive',
                'id_number' => 'nullable|string|max:255|unique:candidate,id_number',
            ]);

            // Verify course exists
            $course = Course::find($validated['course_id']);
            if (!$course) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Course not found. Please select a valid course.'
                ], 422);
            }

            // Verify party exists if provided
            $party = null;
            if (!empty($validated['party_id'])) {
                $party = ElectionParty::find($validated['party_id']);
                if (!$party) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Party not found. Please select a valid party.'
                    ], 422);
                }
            }

            // Create new candidate instance
            $candidate = new Candidate();
            $candidate->name = $validated['name'];
            $candidate->description = $validated['description'];
            $candidate->course_id = $course->id;
            $candidate->electionparty_id = $party ? $party->id : null;
            $candidate->platforms = $validated['platforms'];
            $candidate->status = $validated['status'];
            $candidate->id_number = $validated['id_number'] ?? null;
            $candidate->created_at = now(); // Manually set created_at

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

            // Load relationships for response
            $candidate->load(['course', 'party']);

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
            $candidate = Candidate::with(['course', 'party'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'candidate' => $candidate
            ]);
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
        Log::info('Update method triggered for candidate ID: ' . $id, $request->except(['profile_img', 'banner_img']));
        
        try {
            $validated = $request->validate([
                'profile_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'banner_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'name' => 'required|string|max:255',
                'id_number' => 'nullable|string|max:255|unique:candidate,id_number,' . $id,
                'course_id' => 'required|exists:courses,id',
                'party_id' => 'nullable|exists:electionparty,id',
                'status' => 'required|string',
                'platforms' => 'required|string',
                'description' => 'required|string',
            ]);
            
            $candidate = Candidate::findOrFail($id);
            
            // Verify course exists
            $course = Course::find($validated['course_id']);
            if (!$course) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Course not found. Please select a valid course.'
                ], 422);
            }
            $candidate->course_id = $course->id;
            
            // Verify party exists if provided
            if (!empty($validated['party_id'])) {
                $party = ElectionParty::find($validated['party_id']);
                if ($party) {
                    $candidate->electionparty_id = $party->id;
                } else {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Party not found. Please select a valid party.'
                    ], 422);
                }
            } else {
                $candidate->electionparty_id = null;
            }
            
            // Handle profile image
            if ($request->hasFile('profile_img')) {
                // Delete old profile image if exists
                if ($candidate->profile_img && Storage::disk('public')->exists('upload/profile-picture/' . $candidate->profile_img)) {
                    Storage::disk('public')->delete('upload/profile-picture/' . $candidate->profile_img);
                }
                
                $profileFile = $request->file('profile_img');
                $profileFilename = time() . '_' . Str::random(10) . '_profile.' . $profileFile->getClientOriginalExtension();
                $profileFile->storeAs('upload/profile-picture', $profileFilename, 'public');
                $candidate->profile_img = $profileFilename;
                
                Log::info('Profile image updated: ' . $profileFilename);
            }
            
            // Handle banner image
            if ($request->hasFile('banner_img')) {
                // Delete old banner image if exists
                if ($candidate->banner_img && Storage::disk('public')->exists('upload/banner/' . $candidate->banner_img)) {
                    Storage::disk('public')->delete('upload/banner/' . $candidate->banner_img);
                }
                
                $bannerFile = $request->file('banner_img');
                $bannerFilename = time() . '_' . Str::random(10) . '_banner.' . $bannerFile->getClientOriginalExtension();
                $bannerFile->storeAs('upload/banner', $bannerFilename, 'public');
                $candidate->banner_img = $bannerFilename;
                
                Log::info('Banner image updated: ' . $bannerFilename);
            }
            
            // Update other fields
            $candidate->name = $validated['name'];
            $candidate->id_number = $validated['id_number'] ?? null;
            $candidate->status = $validated['status'];
            $candidate->platforms = $validated['platforms'];
            $candidate->description = $validated['description'];
            
            $candidate->save();
            
            // Load relationships for response
            $candidate->load(['course', 'party']);
            
            Log::info('Candidate updated successfully with ID: ' . $id);
            
            return response()->json([
                'success' => true, 
                'message' => 'Candidate updated successfully.',
                'candidate' => $candidate
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error updating candidate: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false, 
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating candidate: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
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
            
            // Build the base query with relationships
            $query = Candidate::with(['course', 'party']);
            
            // Apply search filter if provided
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                Log::info('Applying search filter: ' . $searchTerm);
                
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('status', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('description', 'LIKE', "%{$searchTerm}%");
                });
            }
            
            // Apply status filter if provided
            if ($request->filled('status')) {
                Log::info('Applying status filter: ' . $request->status);
                $query->where('status', $request->status);
            }
            
            // Apply course filter if provided
            if ($request->filled('course')) {
                Log::info('Applying course filter: ' . $request->course);
                $query->where('course_id', $request->course);
            }
            
            // Execute the paginated query
            Log::info('Executing paginated query...');
            $candidates = $query->orderBy('created_at', 'desc')->paginate(10);
            Log::info('Pagination successful, count: ' . $candidates->count());
            
            // Transform the data to include relationships
            $candidatesData = $candidates->map(function($candidate) {
                return [
                    'id' => $candidate->id,
                    'name' => $candidate->name,
                    'description' => $candidate->description,
                    'platforms' => $candidate->platforms,
                    'status' => $candidate->status,
                    'id_number' => $candidate->id_number,
                    'profile_img' => $candidate->profile_img,
                    'banner_img' => $candidate->banner_img,
                    'created_at' => $candidate->created_at,
                    'course' => $candidate->course ? [
                        'id' => $candidate->course->id,
                        'name' => $candidate->course->name
                    ] : null,
                    'party' => $candidate->party ? [
                        'id' => $candidate->party->id,
                        'name' => $candidate->party->name
                    ] : null
                ];
            });
            
            // Generate pagination HTML
            $paginationHtml = $candidates->links()->render();
            
            Log::info('paginateCandidates successful');
            return response()->json([
                'success' => true,
                'data' => $candidatesData,
                'pagination_html' => $paginationHtml,
                'metadata' => [
                    'total' => $candidates->total(),
                    'page' => $candidates->currentPage(),
                    'last_page' => $candidates->lastPage(),
                    'per_page' => $candidates->perPage()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in paginateCandidates: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
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
     * Get candidate details in JSON format.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showJson($id)
    {
        try {
            $candidate = Candidate::with(['course', 'party'])->findOrFail($id);
            
            $candidateData = [
                'id' => $candidate->id,
                'name' => $candidate->name,
                'description' => $candidate->description,
                'platforms' => $candidate->platforms,
                'status' => $candidate->status,
                'id_number' => $candidate->id_number,
                'profile_img' => $candidate->profile_img,
                'banner_img' => $candidate->banner_img,
                'created_at' => $candidate->created_at,
                'course' => $candidate->course ? [
                    'id' => $candidate->course->id,
                    'name' => $candidate->course->name
                ] : null,
                'party' => $candidate->party ? [
                    'id' => $candidate->party->id,
                    'name' => $candidate->party->name
                ] : null
            ];
            
            return response()->json([
                'success' => true,
                'candidate' => $candidateData
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching candidate: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Candidate not found'], 404);
        }
    }
}