<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Election;

class ViewElectionController extends Controller
{
    public function index()
    {
        $elections = Election::all(); // Fetch elections from the database
        return view('admin.view-election', compact('elections'));
    }
}
