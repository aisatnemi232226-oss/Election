<?php

namespace App\Http\Controllers;

use App\Imports\UsersVotersImport;
use App\Models\Position;
use App\Models\PositionVote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class VoterController extends Controller
{
    /**
     * Display a listing of the voters.
     */
    public function index(Request $request)
    {
        $query = User::query();
        $courseFilter = $request->query('course', '');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('idnumber', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($courseFilter === '__none__') {
            $query->where(function ($courseQuery) {
                $courseQuery->whereNull('course')->orWhere('course', '');
            });
        } elseif ($courseFilter !== '') {
            $query->where('course', $courseFilter);
        }

        $voters = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->only('search', 'course'));

        $positions = collect();
        if (Schema::hasTable('positions')) {
            $positions = Position::query()->orderBy('name')->get();
        }

        $courses = User::query()
            ->whereNotNull('course')
            ->where('course', '<>', '')
            ->distinct()
            ->orderBy('course')
            ->pluck('course');

        if ($request->ajax()) {
            return view('admin.voters.table', compact('voters'));
        }

        return view('admin.voters', compact('voters', 'positions', 'courses', 'courseFilter'));
    }

    public function resetVotesAll($id)
    {
        if (!Schema::hasTable('position_votes')) {
            return back()->with('error', 'Votes table not found.');
        }

        PositionVote::query()->where('user_id', $id)->delete();

        return back()->with('success', 'All votes have been reset for this voter.');
    }

    public function resetVotesPosition(Request $request, $id)
    {
        $request->validate([
            'position_id' => ['required', 'integer'],
        ]);

        if (!Schema::hasTable('position_votes')) {
            return back()->with('error', 'Votes table not found.');
        }

        PositionVote::query()
            ->where('user_id', $id)
            ->where('position_id', $request->input('position_id'))
            ->delete();

        return back()->with('success', 'Vote has been reset for the selected position.');
    }

    /**
     * Show the form for creating a new voter.
     */
    public function create()
    {
        return view('admin.voters.create');
    }

    /**
     * Store a newly created voter in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'idnumber' => 'required|string|unique:users,idnumber',
            'course' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'admin_role' => 'nullable|string',
            'status' => 'required|in:Active,Inactive',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('voter_images', 'public');
        }

        User::create($data);

        return redirect()->route('admin.voters.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified voter.
     */
    public function show($id)
    {
        $voter = User::findOrFail($id);
        return response()->json($voter);
    }

    /**
     * Show the form for editing the specified voter.
     */
    public function edit($id)
    {
        $voter = User::findOrFail($id);
        return view('admin.voters.edit', compact('voter'));
    }

    /**
     * Update the specified voter in storage.
     */
    public function update(Request $request, $id)
    {
        $voter = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'idnumber' => 'required|string|unique:users,idnumber,' . $id,
            'course' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'admin_role' => 'nullable|string',
            'status' => 'required|in:Active,Inactive',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->except('password');

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($voter->profile_image) {
                Storage::disk('public')->delete($voter->profile_image);
            }
            $data['profile_image'] = $request->file('profile_image')->store('voter_images', 'public');
        }

        $voter->update($data);

        return redirect()->route('admin.voters.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified voter from storage.
     */
    public function destroy($id)
    {
        $voter = User::findOrFail($id);
        $voter->delete();

        return redirect()->route('admin.voters.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Reset voter password.
     */
    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => 'required|string|min:6|confirmed'
        ]);

        $voter = User::findOrFail($id);
        $voter->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json(['success' => true, 'message' => 'Password reset successfully.']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'importFile' => 'required|file|mimes:csv,xlsx,xls',
        ]);

        try {
            $import = new UsersVotersImport();
            Excel::import($import, $request->file('importFile'));

            $successCount = $import->getSuccessCount();
            $duplicateCount = $import->getDuplicateCount();
            $failures = $import->getFailures();

            $message = "Import completed: {$successCount} users imported successfully.";
            if ($duplicateCount > 0) {
                $message .= " {$duplicateCount} duplicate entries were skipped.";
            }

            if (!empty($failures)) {
                return redirect()->back()->with('success', $message)->with('importFailures', $failures);
            }

            return redirect()->back()->with('success', $message);
        } catch (\Throwable $e) {
            Log::error('Users import error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An unexpected error occurred during import: ' . $e->getMessage());
        }
    }

    public function downloadSample()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sample_users.csv"'
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['name', 'idnumber', 'email', 'course', 'year_level', 'status']);
            fputcsv($file, ['Juan Dela Cruz', '2026-0001', 'juan@example.com', 'BSIT', '1st Year', 'Active']);
            fputcsv($file, ['Maria Santos', '2026-0002', 'maria@example.com', 'BSBA', '2nd Year', 'Active']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}