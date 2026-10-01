<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email-otp');
    }

    public function showRecoveryQrForm()
    {
        return view('auth.passwords.recovery-qr-scan');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validated = $request->validate(['email' => 'required|email']);
        $email = Str::lower(trim($validated['email']));

        $user = User::query()->where('email', $email)->whereNull('deleted_at')->first();
        $limiterKey = 'password-otp-email:'.hash('sha256', $email);

        if ($user && ! RateLimiter::tooManyAttempts($limiterKey, 1)) {
            $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            DB::table('password_reset_otps')->updateOrInsert(
                ['email' => $email],
                [
                    'code_hash' => Hash::make($otp),
                    'reset_token_hash' => null,
                    'attempts' => 0,
                    'verified_at' => null,
                    'expires_at' => now()->addMinutes(10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            try {
                Mail::to($user->email)->send(new PasswordResetOtpMail($otp));
                RateLimiter::hit($limiterKey, 60);
            } catch (TransportExceptionInterface $exception) {
                Log::error('Password reset OTP email could not be sent.', [
                    'exception' => $exception,
                ]);
                DB::table('password_reset_otps')->where('email', $email)->delete();
                $request->session()->forget('password_otp_email');

                return redirect()
                    ->route('password.request')
                    ->withInput(['email' => $email])
                    ->with('mail_error', 'We could not send the verification email. Please try again shortly or contact support.');
            }
        }

        $request->session()->put('password_otp_email', $email);

        return redirect()->route('password.otp.form')->with(
            'status',
            'If an account exists for that email, a verification code has been sent. Check your inbox.'
        );
    }

    public function showOtpForm(Request $request)
    {
        if (! $request->session()->has('password_otp_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.passwords.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $validated = $request->validate(['otp' => 'required|digits:6']);
        $email = $request->session()->get('password_otp_email');

        if (! $email) {
            return redirect()->route('password.request');
        }

        $resetGrant = null;
        $verified = DB::transaction(function () use ($email, $validated, &$resetGrant): bool {
            $record = DB::table('password_reset_otps')
                ->where('email', $email)
                ->lockForUpdate()
                ->first();

            if (! $record || $record->attempts >= 5 || now()->greaterThan($record->expires_at)) {
                return false;
            }

            if (! $record->code_hash || ! Hash::check($validated['otp'], $record->code_hash)) {
                DB::table('password_reset_otps')->where('email', $email)->increment('attempts');
                return false;
            }

            $resetGrant = Str::random(64);
            DB::table('password_reset_otps')->where('email', $email)->update([
                'code_hash' => null,
                'reset_token_hash' => Hash::make($resetGrant),
                'verified_at' => now(),
                'updated_at' => now(),
            ]);

            return true;
        });

        if (! $verified) {
            return back()->withErrors(['otp' => 'The code is invalid or expired. Request a new code and try again.']);
        }

        $request->session()->put('password_reset_grant', $resetGrant);

        return redirect()->route('password.reset');
    }

    public function verifyRecoveryQr(Request $request)
    {
        $validated = $request->validate(['qr' => 'required|string|max:2000']);
        $payload = json_decode($validated['qr'], true);

        if (! is_array($payload)
            || ($payload['purpose'] ?? null) !== 'password-recovery'
            || ! isset($payload['user_id'], $payload['secret'])
            || ! is_numeric($payload['user_id'])
            || ! is_string($payload['secret'])) {
            return response()->json(['message' => 'Invalid or expired recovery QR.'], 422);
        }

        $user = DB::transaction(function () use ($payload) {
            $user = User::query()->whereKey($payload['user_id'])->lockForUpdate()->first();

            if (! $user
                || $user->deleted_at !== null
                || ! filter_var($user->email, FILTER_VALIDATE_EMAIL)
                || ! $user->password_recovery_qr_hash
                || ! Hash::check($payload['secret'], $user->password_recovery_qr_hash)) {
                return null;
            }

            $user->forceFill(['password_recovery_qr_hash' => null])->save();

            return $user;
        });

        if (! $user) {
            return response()->json(['message' => 'Invalid or expired recovery QR. Generate a new one while signed in.'], 422);
        }

        $resetGrant = Str::random(64);
        DB::table('password_reset_otps')->updateOrInsert(
            ['email' => $user->email],
            [
                'code_hash' => null,
                'reset_token_hash' => Hash::make($resetGrant),
                'attempts' => 0,
                'verified_at' => now(),
                'expires_at' => now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $request->session()->regenerate();
        $request->session()->put([
            'password_otp_email' => $user->email,
            'password_reset_grant' => $resetGrant,
        ]);

        return response()->json([
            'ok' => true,
            'redirect' => route('password.reset'),
        ]);
    }
}
