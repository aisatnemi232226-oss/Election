<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        $prefillName = trim((string) request()->query('name', ''));
        $nameParts = preg_split('/\s+/', $prefillName);
        $derivedFirstName = $nameParts[0] ?? '';
        $derivedLastName = count($nameParts) > 1 ? $nameParts[count($nameParts) - 1] : '';
        $derivedMiddleInitial = count($nameParts) > 2
            ? strtoupper(substr((string) ($nameParts[1] ?? ''), 0, 1)) . '.'
            : '';

        return view('voter.register', [
            'prefillIdNumber' => request()->query('idnumber', ''),
            'prefillEmail' => request()->query('email', ''),
            'prefillFirstName' => request()->query('first_name', ''),
            'prefillMiddleInitial' => request()->query('middle_initial', ''),
            'prefillLastName' => request()->query('last_name', ''),
            'prefillName' => $prefillName,
            'prefillNameFirstPart' => $derivedFirstName,
            'prefillNameMiddleInitial' => $derivedMiddleInitial,
            'prefillNameLastPart' => $derivedLastName,
            'redirectTo' => request()->query('redirect_to', ''),
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'idnumber' => 'required|regex:/^\d+$/|unique:users,idnumber',
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:10|regex:/^[A-Za-z](?:\.)?$/',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        $firstName = trim($request->input('first_name'));
        $middleInitial = trim((string) $request->input('middle_initial', ''));
        if ($middleInitial !== '') {
            $middleInitial = strtoupper(rtrim($middleInitial, '.')) . '.';
        }
        $lastName = trim($request->input('last_name'));

        $loginQrSecret = Str::random(64);
        $recoveryQrSecret = Str::random(64);
        $user = User::create([
            'idnumber' => $request->idnumber,
            'first_name' => $firstName,
            'middle_initial' => $middleInitial ?: null,
            'last_name' => $lastName,
            'name' => trim(implode(' ', array_filter([$firstName, $middleInitial, $lastName]))),
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->forceFill([
            'login_qr_hash' => Hash::make($loginQrSecret),
            'password_recovery_qr_hash' => Hash::make($recoveryQrSecret),
        ])->save();

        $request->session()->flash('login_qr_secret', $loginQrSecret);
        $request->session()->flash('recovery_qr_secret', $recoveryQrSecret);
        $redirectTo = (string) $request->input('redirect_to', '');
        if ($redirectTo !== '' && str_starts_with($redirectTo, '/') && ! str_contains($redirectTo, '//')) {
            $request->session()->flash('recovery_qr_continue_to', $redirectTo);
        }
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('account.qr-codes')->with(
            'status',
            'Your voter account has been created and is awaiting administrator verification. You can vote after an administrator verifies your student details.'
        );
    }
}
