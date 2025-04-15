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
use Purifier;

class CandidateController extends Controller
{
    public function index()
    {
    $candidates = Candidate::with(['user', 'course', 'party'])->get();
    return view('admin.candidates', compact('candidates'));
    }


    public function newCandidate()
    {
        $candidate = new Candidate(); // Create a new empty Candidate instance
        $parties = ElectionParty::all(); // Fetch all election parties
        return view('admin.new-candidate', compact('candidate', 'parties'));
    }

    public function editCandidate($id)
    {
        $candidate = Candidate::with('course')->findOrFail($id);
        $parties = ElectionParty::all(); // Fetch all parties

        return view('admin.edit-candidate', compact('candidate', 'parties'));
    }

    public function update(Request $request, $id)
    {
        \Log::info('Update method triggered', $request->all());

        $request->validate([
            'profile_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'id_number' => 'required|string|max:255|unique:candidate,id_number,' . $id,
            'course' => 'required|string|max:255',
            'party' => 'required|string|max:255',
            'status' => 'required|string',
            'platforms' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        $candidate = Candidate::findOrFail($id);
        $course = Course::where('name', $request->course)->first();

        if (!$course) {
            return back()->withErrors(['course' => 'Course not found. Please select a valid course.']);
        }

        $party = ElectionParty::find($request->party);

        if (!$party) {
            return back()->withErrors(['party' => 'Party not found. Please select a valid Party.']);
        }


        // Handle Profile Image Update
        if ($request->hasFile('profile_img')) {
            // Delete old profile image if it exists
            if ($candidate->profile_img && file_exists(storage_path('app/public/upload/profile-picture/' . $candidate->profile_img))) {
                unlink(storage_path('app/public/upload/profile-picture/' . $candidate->profile_img));
            }

            $profileFile = $request->file('profile_img');
            $profileFilename = time() . '_profile.' . $profileFile->getClientOriginalExtension();
            $profileFile->storeAs('upload/profile-picture', $profileFilename, 'public');
            $candidate->profile_img = $profileFilename;
        }

        // Handle Banner Image Update
        if ($request->hasFile('banner_img')) {
            // Delete old banner image if it exists
            if ($candidate->banner_img && file_exists(storage_path('app/public/upload/banner/' . $candidate->banner_img))) {
                unlink(storage_path('app/public/upload/banner/' . $candidate->banner_img));
            }

            $bannerFile = $request->file('banner_img');
            $bannerFilename = time() . '_banner.' . $bannerFile->getClientOriginalExtension();
            $bannerFile->storeAs('upload/banner', $bannerFilename, 'public');
            $candidate->banner_img = $bannerFilename;
        }

        // Update candidate details
        $cleanPlatforms = Purifier::clean($request->platforms);
        $cleanDescription = Purifier::clean($request->description);

        $candidate->update([
            'name' => $request->name,
            'id_number' => $request->id_number,
            'course_id' => $course->id,
            'electionparty_id' => $party->id,
            'status' => $request->status,
            'platforms' => $cleanPlatforms,
            'description' => $cleanDescription,
        ]);
        return redirect()->route('admin.candidates.index')->with('success', 'Candidate updated successfully.');
    }


    public function store(Request $request)
    {
        
        \Log::info('Store method triggered', $request->all());

        $request->validate([
            'profile_img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner_img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'id_number' => 'required|string|max:255|unique:candidate,id_number',
            'course' => 'required|string|max:255',
            'party' => 'required|string|max:255|exists:electionparty,id',
            'status' => 'required|string',
            'platforms' => 'required|string',
            'description' => 'required|string',
        ]);

        $course = Course::where('name', $request->course)->first();

        if (!$course) {
            return back()->withErrors(['course' => 'Course not found. Please select a valid course.']);
        }

        $party = ElectionParty::find($request->party);

        if (!$party) {
            return back()->withErrors(['party' => 'Party not found. Please select a valid Party.']);
        }

        // Initialize filenames
        $profileFilename = null;
        $bannerFilename = null;

        // Store Profile Picture
        if ($request->hasFile('profile_img')) {
            $profileFile = $request->file('profile_img');
            $profileFilename = time() . '_profile.' . $profileFile->getClientOriginalExtension();
            $profileFile->storeAs('upload/profile-picture', $profileFilename, 'public');
        }

        // Store Banner
        if ($request->hasFile('banner_img')) {
            $bannerFile = $request->file('banner_img');
            $bannerFilename = time() . '_banner.' . $bannerFile->getClientOriginalExtension();
            $bannerFile->storeAs('upload/banner', $bannerFilename, 'public');
        }

        // Save candidate data to database
        $cleanPlatforms = Purifier::clean($request->platforms);
        $cleanDescription = Purifier::clean($request->description);
        
        Candidate::create([
            'name' => $request->name,
            'id_number' => $request->id_number,
            'course_id' => $course->id,
            'electionparty_id' => $party->id,
            'status' => $request->status,
            'platforms' => $cleanPlatforms,
            'description' => $cleanDescription,
            'profile_img' => $profileFilename,
            'banner_img' => $bannerFilename,
        ]);        

        return redirect()->route('admin.candidates.index');
    }

    public function filter(Request $request)
    {
        $search = $request->query('search');

        // Query to fetch the voter's details along with the course name
        $verifiedVoters = VerifiedVoter::join('users', 'verified_voters.users_id', '=', 'users.id')
            ->join('courses', 'verified_voters.course_id', '=', 'courses.id') // Join courses table
            ->where('users.idnumber', 'LIKE', "%$search%")
            ->select('users.idnumber', 'users.name', 'courses.name as course_name') // Select course_name from courses
            ->get();

        return response()->json($verifiedVoters);
    }


    public function destroy($id)
    {
        $candidate = Candidate::findOrFail($id);
        $candidate->delete(); // Soft delete
        return redirect()->route('admin.candidates.index');
    }

    public function uploadQuillImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // Ensure file is valid
            if (!$file->isValid()) {
                return response()->json(['error' => 'Invalid file.'], 400);
            }

            // Generate unique filename using UUID
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Store the image in 'public/upload/quill-images'
            $path = $file->storeAs('public/upload/quill-images', $filename);

            // Return public URL to the uploaded file using Storage::url
            return response()->json([
                'url' => Storage::url('upload/quill-images/' . $filename)
            ]);
        }

        return response()->json(['error' => 'No image uploaded.'], 400);
    }

}
