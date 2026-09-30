<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Acadyr;
use App\Models\Semesters;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $candidates = Candidate::query()
            ->where('status', 'Pending')
            ->orderByDesc('created_at')
            ->select(['id', 'name', 'course_id', 'position_id', 'created_at'])
            ->limit(8)
            ->get();

        $voters = User::query()
            ->where('is_admin', false)
            ->whereNotNull('voter_verification_submitted_at')
            ->whereDoesntHave('verifiedVoter')
            ->orderByDesc('id')
            ->select(['id', 'name', 'idnumber', 'voter_verification_submitted_at'])
            ->limit(8)
            ->get();

        return response()->json([
            'counts' => [
                'candidates_pending' => Candidate::query()->where('status', 'Pending')->count(),
                'voters_pending_verification' => User::query()
                    ->where('is_admin', false)
                    ->whereNotNull('voter_verification_submitted_at')
                    ->whereDoesntHave('verifiedVoter')
                    ->count(),
            ],
            'candidates' => $candidates,
            'voters' => $voters,
            'academic_years' => Acadyr::query()->orderByDesc('id')->get(['id', 'name']),
            'semesters' => Semesters::query()->orderBy('id')->get(['id', 'name']),
        ]);
    }
}
