<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        $qrName = $request->query('name');
        $qrIdNumber = $request->query('idnumber');
        $qrEmail = $request->query('email');
        $nameParts = preg_split('/\s+/', trim(is_string($qrName) ? $qrName : ''), -1, PREG_SPLIT_NO_EMPTY);

        return view('voter.account-register', [
            'prefillIdNumber' => is_string($qrIdNumber) ? $qrIdNumber : '',
            'prefillEmail' => is_string($qrEmail) ? $qrEmail : '',
            'prefillFirstName' => $nameParts[0] ?? '',
            'prefillMiddleInitial' => count($nameParts) > 2
                ? strtoupper(substr((string) $nameParts[1], 0, 1)) . '.'
                : '',
            'prefillLastName' => count($nameParts) > 1 ? $nameParts[count($nameParts) - 1] : '',
        ]);
    }

    public function createAccount(Request $request)
    {
        $validated = $request->validate([
            'idnumber' => ['required', 'string', 'max:32', 'unique:users,idnumber'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:10', 'regex:/^[\pL]\.?$/u'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'regex:/^[^@\s]+@pcu\.edu\.ph$/i',
                'max:255',
                'unique:users,email',
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $name = trim(implode(' ', array_filter([
            $validated['first_name'],
            $validated['middle_initial'] ?? null,
            $validated['last_name'],
        ])));

        User::create([
            'idnumber' => $validated['idnumber'],
            'first_name' => $validated['first_name'],
            'middle_initial' => $validated['middle_initial'] ?? null,
            'last_name' => $validated['last_name'],
            'name' => $name,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => false,
        ]);

        return redirect()->route('register')->with(
            'status',
            'Your account has been created. Sign in to apply separately for voter verification.'
        );
    }

    public function showVoterVerificationForm()
    {
        $user = request()->user();

        $prefillName = trim((string) $user->name);
        $nameParts = preg_split('/\s+/', $prefillName);
        $derivedFirstName = $nameParts[0] ?? '';
        $derivedLastName = count($nameParts) > 1 ? $nameParts[count($nameParts) - 1] : '';
        $derivedMiddleInitial = count($nameParts) > 2
            ? strtoupper(substr((string) ($nameParts[1] ?? ''), 0, 1)) . '.'
            : '';

        $firstName = (string) ($user?->first_name ?: $derivedFirstName);
        $middleInitial = (string) ($user?->middle_initial ?: $derivedMiddleInitial);
        $lastName = (string) ($user?->last_name ?: $derivedLastName);

        return view('voter.register', [
            'prefillIdNumber' => $user->idnumber,
            'prefillEmail' => $user->email,
            'prefillFirstName' => $firstName,
            'prefillMiddleInitial' => $middleInitial,
            'prefillLastName' => $lastName,
            'alreadyVerified' => $user->isVerifiedVoter(),
            'verificationPending' => $user->voter_verification_submitted_at !== null,
        ]);
    }

    public function submitVoterVerification(Request $request)
    {
        $user = $request->user();

        if ($user->isVerifiedVoter()) {
            return redirect()->route('register.verify');
        }

        if ($user->voter_verification_submitted_at !== null) {
            return redirect()->route('register.verify');
        }

        $request->validate([
            'current_password' => 'required|string|current_password',
        ]);

        $user->voter_verification_submitted_at = now();
        $user->save();

        return redirect()->route('register.verify')->with(
            'status',
            'Your existing account has been submitted for administrator verification. You can vote after an administrator verifies your student details.'
        );
    }
}
