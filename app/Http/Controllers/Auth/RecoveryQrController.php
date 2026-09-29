<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RecoveryQrController extends Controller
{
    public function show(Request $request)
    {
        $continueTo = $request->session()->pull('recovery_qr_continue_to', route('home'));
        if (! is_string($continueTo) || ! str_starts_with($continueTo, '/') || str_contains($continueTo, '//')) {
            $continueTo = route('home');
        }

        return view('auth.passwords.recovery-qr', [
            'loginSecret' => $request->session()->pull('login_qr_secret'),
            'recoverySecret' => $request->session()->pull('recovery_qr_secret'),
            'userId' => $request->user()->getKey(),
            'continueTo' => $continueTo,
        ]);
    }

    public function generate(Request $request)
    {
        $secret = Str::random(64);

        $request->user()->forceFill([
            'password_recovery_qr_hash' => Hash::make($secret),
        ])->save();

        $request->session()->flash('recovery_qr_secret', $secret);

        return redirect()->route('account.qr-codes');
    }

    public function generateLogin(Request $request)
    {
        $secret = Str::random(64);

        $request->user()->forceFill([
            'login_qr_hash' => Hash::make($secret),
        ])->save();

        $request->session()->flash('login_qr_secret', $secret);

        return redirect()->route('account.qr-codes');
    }
}