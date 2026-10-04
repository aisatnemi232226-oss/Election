<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Course;
use App\Models\Election;
use App\Models\ElectionParty;
use App\Models\PositionVote;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CandidateRegistrationController extends Controller
{
    public function show(Request $request)
    {
        $prefillIdNumberRaw = (string) $request->query('idnumber', '');
        $prefillEmail = (string) $request->query('email', '');
        $prefillName = (string) $request->query('name', '');
        $prefillPositionId = (string) $request->query('position_id', '');

        $idNumber = preg_replace('/[^0-9]/', '', $prefillIdNumberRaw);
        if (!$idNumber && Auth::check()) {
            $idNumber = preg_replace('/[^0-9]/', '', (string) (Auth::user()->idnumber ?? ''));
        }
        $credentialValue = Auth::check()
            ? ((string) (Auth::user()->idnumber ?: Auth::user()->email))
            : ($prefillIdNumberRaw ?: $prefillEmail);

        $hasExistingApplication = false;
        $existingApplicationStatus = null;
        if ($idNumber) {
            $existing = Candidate::query()
                ->where('id_number', $idNumber)
                ->whereNull('deleted_at')
                ->orderByDesc('created_at')
                ->first();

            if ($existing) {
                $existingApplicationStatus = $existing->status;

                if (in_array($existing->status, ['Pending', 'Active'], true)) {
                    $hasExistingApplication = true;
                }
                $existingApplicationStatus = $existing->status;
            }
        }

        return view('voter.candidate-register', [
            'elections' => Election::query()->orderByDesc('created_at')->orderByDesc('id')->get(),
            'prefillElectionId' => (string) $request->query('election_id', ''),
            'courses' => Course::query()
                ->orderBy('name')
                ->orderBy('id')
                ->get()
                ->unique(function (Course $course) {
                    return mb_strtolower(trim((string) $course->name));
                })
                ->values(),
            'parties' => ElectionParty::query()->orderBy('name')->get(),
            'positions' => Position::query()->orderBy('name')->get(),
            'prefillIdNumber' => $prefillIdNumberRaw,
            'prefillEmail' => $prefillEmail,
            'prefillName' => $prefillName,
            'prefillPositionId' => $prefillPositionId,
            'credentialValue' => $credentialValue,
            'hasExistingApplication' => $hasExistingApplication,
            'existingApplicationStatus' => $existingApplicationStatus,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'election_id' => 'required|integer|exists:election,id',
            'idnumber' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'course_id' => 'required|exists:courses,id',
            'position_id' => 'required|exists:positions,id',
            'party_id' => 'nullable|exists:electionparty,id',
            'description' => 'required|string',
            'platforms' => 'required|string',
            'profile_img' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            'banner_img' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $idNumber = preg_replace('/[^0-9]/', '', (string) $validated['idnumber']);

        $user = Auth::user();
        if (!$user) {
            $user = User::query()->where('idnumber', $idNumber)->orWhere('email', $validated['email'])->first();
            if (!$user) {
                $redirectTo = route('candidate.register', ['position_id' => (int) $validated['position_id']]);
                return redirect()->route('register', [
                    'idnumber' => $idNumber,
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'redirect_to' => $redirectTo,
                ]);
            }
        }

        $existingCandidate = Candidate::withTrashed()
            ->where('id_number', $idNumber)
            ->first();

        if (
            $existingCandidate
            && $existingCandidate->election_id
            && (int) $existingCandidate->election_id !== (int) $validated['election_id']
            && PositionVote::query()->where('candidate_id', $existingCandidate->id)->exists()
        ) {
            return back()->withErrors([
                'election_id' => 'This candidate already has recorded votes in another election and cannot be reassigned.',
            ])->withInput();
        }

        if ($existingCandidate && !$existingCandidate->trashed() && in_array($existingCandidate->status, ['Pending', 'Active'], true)) {
            return back()->withErrors([
                'idnumber' => 'You have already submitted a candidate application. Please wait for the admin review.',
            ])->withInput();
        }

        $partyId = $request->filled('party_id') ? (int) $validated['party_id'] : null;
        if ($partyId) {
            $partyPositionTaken = Candidate::query()
                ->where('electionparty_id', $partyId)
                ->where('election_id', (int) $validated['election_id'])
                ->where('position_id', (int) $validated['position_id'])
                ->whereNull('deleted_at')
                ->whereIn('status', ['Pending', 'Active'])
                ->when($existingCandidate, function ($q) use ($existingCandidate) {
                    $q->where('id', '!=', $existingCandidate->id);
                })
                ->exists();

            if ($partyPositionTaken) {
                return back()->withErrors([
                    'position_id' => 'Position already been filled for this party. Please choose a different position or register as Independent (No Party).',
                ])->withInput();
            }
        }

        $storedFiles = [];
        $submissionSaved = false;

        try {
            $candidate = $existingCandidate ?: new Candidate();
            if (Schema::hasColumn('candidate', 'users_id')) {
                $candidate->users_id = $user ? $user->id : null;
            }
            $candidate->name = $validated['name'];
            $candidate->election_id = (int) $validated['election_id'];
            $candidate->id_number = $idNumber;
            $candidate->course_id = (int) $validated['course_id'];
            $candidate->position_id = (int) $validated['position_id'];
            $candidate->electionparty_id = $partyId;
            $candidate->platforms = $validated['platforms'];
            $candidate->description = $validated['description'];
            $candidate->status = 'Pending';
            if ($candidate->trashed()) {
                $candidate->deleted_at = null;
            }

            if ($request->hasFile('profile_img')) {
                $profileFile = $request->file('profile_img');
                $profileFilename = time() . '_' . Str::random(10) . '_profile.' . $profileFile->getClientOriginalExtension();
                $storedPath = $profileFile->storeAs('upload/profile-picture', $profileFilename, 'public');
                if (!is_string($storedPath)) {
                    return back()->withErrors([
                        'profile_img' => 'The profile image could not be saved. Please try again.',
                    ])->withInput();
                }
                $storedFiles[] = $storedPath;
                $candidate->profile_img = $profileFilename;
            }

            if ($request->hasFile('banner_img')) {
                $bannerFile = $request->file('banner_img');
                $bannerFilename = time() . '_' . Str::random(10) . '_banner.' . $bannerFile->getClientOriginalExtension();
                $storedPath = $bannerFile->storeAs('upload/banner', $bannerFilename, 'public');
                if (!is_string($storedPath)) {
                    return back()->withErrors([
                        'banner_img' => 'The banner image could not be saved. Please try again.',
                    ])->withInput();
                }
                $storedFiles[] = $storedPath;
                $candidate->banner_img = $bannerFilename;
            }

            DB::transaction(function () use ($candidate): void {
                $candidate->save();
            });
            $submissionSaved = true;

            return redirect()->route('candidate.register')->with('success', 'Application submitted. Please wait for admin approval.');
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return redirect()->route('candidate.register')->withErrors([
                'idnumber' => 'You have already submitted a candidate application. Please wait for the admin review.',
            ]);
        } catch (QueryException $e) {
            if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
                return redirect()->route('candidate.register')->withErrors([
                    'idnumber' => 'You have already submitted a candidate application. Please wait for the admin review.',
                ]);
            }
            throw $e;
        } finally {
            if (!$submissionSaved && $storedFiles !== []) {
                Storage::disk('public')->delete($storedFiles);
            }
        }
    }
}
