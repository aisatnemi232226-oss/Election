<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\PositionVote;
use App\Models\VerifiedVoter;
use App\Services\ElectionWidgetService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Database\UniqueConstraintViolationException;

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

        $election = $candidate->election_id
            ? Election::query()->find($candidate->election_id)
            : null;
        if (! $election) {
            return back()->with('error', 'This candidate is not linked to an available election.');
        }

        if (! $election->start_date || ! $election->end_date) {
            return back()->with('error', 'Voting dates have not been configured for this election.');
        }

        $now = now(config('app.timezone'));
        $votingStartsAt = Carbon::parse($election->start_date, config('app.timezone'))->startOfDay();
        $votingEndsAt = Carbon::parse($election->end_date, config('app.timezone'))->endOfDay();
        if ($now->lt($votingStartsAt) || $now->gt($votingEndsAt)) {
            return back()->with('error', 'Voting is not currently open for this election.');
        }

        if (!$candidate->position_id) {
            return back()->with('error', 'Invalid candidate position.');
        }

        $userId = $user->id;

        $alreadyVoted = PositionVote::query()
            ->where('user_id', $userId)
            ->where('election_id', $election->id)
            ->where('position_id', $candidate->position_id)
            ->exists();

        if ($alreadyVoted) {
            return back()->with('error', 'You have already voted for this position.');
        }

        try {
            PositionVote::query()->create([
                'user_id' => $userId,
                'election_id' => $election->id,
                'position_id' => $candidate->position_id,
                'candidate_id' => $candidate->id,
            ]);
            app(ElectionWidgetService::class)->forget((int) $election->id);
        } catch (UniqueConstraintViolationException $e) {
            return back()->with('error', 'You have already voted for this position.');
        } catch (QueryException $e) {
            if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
                return back()->with('error', 'You have already voted for this position.');
            }

            throw $e;
        }

        return back()->with('success', 'Your vote has been recorded.');
    }
}
