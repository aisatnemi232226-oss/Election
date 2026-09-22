<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\PositionVote;
use App\Models\VerifiedVoter;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function cast(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to vote.');
        }

        if ($user->is_admin) {
            return back()->with('error', 'Admins are not allowed to vote in student elections.');
        }

        if (!$user->isVerifiedVoter()) {
            return back()->with('error', 'You are not a verified voter yet. Please contact an administrator to complete your verification before voting.');
        }

        $request->validate([
            'candidate_id' => ['required', 'integer'],
        ]);

        $candidate = Candidate::query()->where('status', 'Active')->findOrFail($request->input('candidate_id'));

        if (!$candidate->position_id) {
            return back()->with('error', 'Invalid candidate position.');
        }

        $userId = $user->id;

        $alreadyVoted = PositionVote::query()
            ->where('user_id', $userId)
            ->where('position_id', $candidate->position_id)
            ->exists();

        if ($alreadyVoted) {
            return back()->with('error', 'You have already voted for this position.');
        }

        try {
            PositionVote::query()->create([
                'user_id' => $userId,
                'position_id' => $candidate->position_id,
                'candidate_id' => $candidate->id,
            ]);
        } catch (QueryException $e) {
            return back()->with('error', 'You have already voted for this position.');
        }

        return back()->with('success', 'Your vote has been recorded.');
    }
}
