<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResultsController extends Controller
{
    /**
     * Display the results page
     */
    public function index()
    {
        // For now, we'll return the view with static data
        // Later you can add dynamic data fetching from the database
        return view('admin.results');
    }
}