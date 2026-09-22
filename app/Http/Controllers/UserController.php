<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->where('is_admin', false)
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }
}
