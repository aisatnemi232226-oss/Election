<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Candidate;
use Carbon\Carbon;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        $type = $request->input('type');

        switch ($type) {
            case 'candidates':
                return $this->searchCandidates($query);
            // Add more cases for other types
            default:
                return response()->json(['error' => 'Invalid search type'], 400);
        }
    }

    private function searchCandidates($query)
    {
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
        $currentUser = auth()->user()->username;

        $candidates = Candidate::where(function($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%")
              ->orWhere('status', 'LIKE', "%{$query}%")
              ->orWhereHas('course', function($q) use ($query) {
                  $q->where('name', 'LIKE', "%{$query}%");
              });
        })
        ->with(['course', 'party'])
        ->paginate(10);

        // Add metadata to the response
        $candidates->additional([
            'metadata' => [
                'timestamp' => $currentDateTime,
                'user' => $currentUser
            ]
        ]);

        return response()->json([
            'success' => true,
            'data' => $candidates,
            'metadata' => [
                'timestamp' => $currentDateTime,
                'user' => $currentUser
            ]
        ]);
    }
}