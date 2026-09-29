<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request)
    {
        if (! $this->hasValidResetGrant($request)) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Your password reset session has expired. Request a new code.',
            ]);
        }

        return view('auth.passwords.reset-otp');
    }

    public function reset(Request $request)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (! $this->hasValidResetGrant($request)) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Your password reset session has expired. Request a new code.',
            ]);
        }

        $email = $request->session()->get('password_otp_email');

        DB::transaction(function () use ($email, $validated): void {
            $user = User::query()->where('email', $email)->whereNull('deleted_at')->firstOrFail();
            $user->forceFill(['password' => $validated['password']])->save();
            DB::table('password_reset_otps')->where('email', $email)->delete();
        });

        $request->session()->forget(['password_otp_email', 'password_reset_grant']);

        return redirect()->route('login')->with('status', 'Your password has been reset. You can now sign in.');
    }

    private function hasValidResetGrant(Request $request): bool
    {
        $email = $request->session()->get('password_otp_email');
        $grant = $request->session()->get('password_reset_grant');

        if (! $email || ! $grant) {
            return false;
        }

        $record = DB::table('password_reset_otps')->where('email', $email)->first();

        return $record
            && $record->verified_at !== null
            && now()->lessThanOrEqualTo($record->expires_at)
            && $record->reset_token_hash
            && Hash::check($grant, $record->reset_token_hash);
    }
}
