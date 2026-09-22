<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('voter.register', [
            'prefillIdNumber' => request()->query('idnumber', ''),
            'prefillEmail' => request()->query('email', ''),
            'prefillName' => request()->query('name', ''),
            'redirectTo' => request()->query('redirect_to', ''),
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'idnumber' => 'required|regex:/^\d+$/|unique:users,idnumber',
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        $user = User::create([
            'idnumber' => $request->idnumber,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        $redirectTo = (string) $request->input('redirect_to', '');
        if ($redirectTo !== '' && str_starts_with($redirectTo, '/') && !str_contains($redirectTo, "//")) {
            return redirect()->to($redirectTo);
        }

        if (Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('home');
    }
}
