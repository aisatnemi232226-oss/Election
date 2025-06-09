<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class VoterController extends Controller
{
    /**
     * Display a listing of the voters.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('idnumber', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $voters = $query->orderBy('created_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            return view('admin.voters.table', compact('voters'));
        }

        return view('admin.voters', compact('voters'));
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
}