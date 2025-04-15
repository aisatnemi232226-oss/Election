<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Election;

class ElectionsController extends Controller
{
    public function index()
    {
        $elections = Election::all();
        $selectedElection = $elections->first();

        return view('admin.elections', compact('elections', 'selectedElection'));
    }

    public function create()
    {
        return view('admin.elections.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        Election::create($request->all());

        session()->flash('success', 'Election created successfully.');
        return redirect()->route('admin.elections.index');
    }

    public function edit(Election $election)
    {
        return view('admin.elections.edit', compact('election'));
    }

    public function update(Request $request, Election $election)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        $election->update($request->all());

        session()->flash('success', 'Election updated successfully.');
        return redirect()->route('admin.elections.index');
    }

    public function destroy(Election $election)
    {
        $election->delete();

        session()->flash('success', 'Election deleted successfully.');
        return redirect()->route('admin.elections.index');
    }

    public function content()
    {
    $elections = Election::all();
    return view('admin.elections', compact('elections'));
    }
}
