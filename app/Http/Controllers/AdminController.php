<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\User;
use App\Models\VerifiedVoter;

class AdminController extends Controller
{
    public function index()
    {
        $totalElections = Election::count();
        $totalCandidates = Candidate::count();
        $totalVoters = VerifiedVoter::count();
        $totalEnrolledStudents = User::where('is_admin', false)->count();

        $recentElections = Election::query()
            ->orderByDesc('id')
            ->take(5)
            ->get()
            ->map(function ($election) {
                $now = now();
                $status = 'Upcoming';

                if (!empty($election->start_date) && !empty($election->end_date)) {
                    if ($now->lt($election->start_date)) {
                        $status = 'Upcoming';
                    } elseif ($now->gt($election->end_date)) {
                        $status = 'Completed';
                    } else {
                        $status = 'Ongoing';
                    }
                }

                $turnout = null;
                try {
                    $row = DB::table('results')
                        ->where('election_id', $election->id)
                        ->orderByDesc('created_at')
                        ->first();

                    if ($row && !empty($row->totalVoters) && $row->totalVoters > 0) {
                        $votes = (int) ($row->totalVotes ?? 0);
                        $voters = (int) $row->totalVoters;
                        $pct = round(($votes / $voters) * 100);
                        $turnout = $pct . '%';
                    }
                } catch (\Exception $e) {
                    $turnout = null;
                }

                $election->dashboard_status = $status;
                $election->dashboard_turnout = $turnout;
                return $election;
            });

        return view('admin.dashboard', compact(
            'totalElections',
            'totalCandidates',
            'totalVoters',
            'totalEnrolledStudents',
            'recentElections'
        ));
    }
}
