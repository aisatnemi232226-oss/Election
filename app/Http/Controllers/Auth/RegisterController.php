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
        $prefillName = trim((string) request()->query('name', ''));
        $nameParts = preg_split('/\s+/', $prefillName, 2);

        return view('voter.register', [
            'prefillIdNumber' => request()->query('idnumber', ''),
            'prefillEmail' => request()->query('email', ''),
            'prefillFirstName' => request()->query('first_name', ''),
            'prefillLastName' => request()->query('last_name', ''),
            'prefillName' => $prefillName,
            'prefillNameFirstPart' => $nameParts[0] ?? '',
            'prefillNameLastPart' => $nameParts[1] ?? '',
            'redirectTo' => request()->query('redirect_to', ''),
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'idnumber' => 'required|regex:/^\d+$/|unique:users,idnumber',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        $firstName = trim($request->input('first_name'));
        $lastName = trim($request->input('last_name'));

        $user = User::create([
            'idnumber' => $request->idnumber,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'name' => trim($firstName . ' ' . $lastName),
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
