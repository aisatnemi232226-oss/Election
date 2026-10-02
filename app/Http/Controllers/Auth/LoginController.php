<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\AuditLogger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('voter.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        // Determine whether the user is logging in with an email or an ID number
        $loginValue = $validated['email'];
        $loginField = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'idnumber';

        $credentials = [
            $loginField => $loginValue,
            'password' => $validated['password'],
        ];

        try {
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                $user = Auth::user();
                if ($user && $user->is_admin) {
                    AuditLogger::log(
                        $request,
                        $user->id,
                        'SECURITY',
                        'LOGIN',
                        'Admin Login',
                        'Admin logged in.',
                        'users',
                        $user->id
                    );
                }

                if (Auth::user()->is_admin) {
                    return redirect()->route('admin.dashboard');
                }

                return redirect()->route('home');
            }
        } catch (\RuntimeException $e) {
            // Handles cases where the stored password is not a valid Bcrypt hash
            return back()->withErrors(['email' => 'Invalid credentials'])->withInput($request->only('email'));
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user && $user->is_admin) {
            AuditLogger::log(
                $request,
                $user->id,
                'SECURITY',
                'LOGOUT',
                'Admin Logout',
                'Admin logged out.',
                'users',
                $user->id
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function loginWithQr(Request $request)
    {
        $validated = $request->validate([
            'qr' => 'required|string',
        ]);

        $qr = trim($validated['qr']);

        if (str_starts_with($qr, '{')) {
            $payload = json_decode($qr, true);

            if (is_array($payload) && ($payload['purpose'] ?? null) === 'login') {
                $user = null;
                if (isset($payload['user_id'], $payload['secret'])
                    && is_numeric($payload['user_id'])
                    && is_string($payload['secret'])) {
                    $user = User::query()
                        ->whereKey($payload['user_id'])
                        ->whereNull('deleted_at')
                        ->first();
                }

                if (! $user || ! $user->login_qr_hash || ! Hash::check($payload['secret'], $user->login_qr_hash)) {
                    return response()->json([
                        'ok' => false,
                        'message' => 'Invalid login QR. Generate a replacement while signed in.',
                    ], 422);
                }

                Auth::login($user);
                $request->session()->regenerate();

                if ($user->is_admin) {
                    AuditLogger::log(
                        $request,
                        $user->id,
                        'SECURITY',
                        'LOGIN',
                        'Admin Login (QR)',
                        'Admin logged in via account QR.',
                        'users',
                        $user->id
                    );
                }

                return response()->json([
                    'ok' => true,
                    'redirect' => $user->is_admin ? route('admin.dashboard') : route('home'),
                ]);
            }
        }

        $idOrEmail = null;
        $idnumber = null;
        $email = null;
        $name = null;

        if ($qr !== '' && str_starts_with($qr, '{')) {
            try {
                $decoded = json_decode($qr, true);
                if (is_array($decoded)) {
                    if (!empty($decoded['idnumber'])) {
                        $idnumber = (string) $decoded['idnumber'];
                        $idOrEmail = $idnumber;
                    } elseif (!empty($decoded['email'])) {
                        $email = (string) $decoded['email'];
                        $idOrEmail = $email;
                    }

                    if (!empty($decoded['email']) && $email === null) {
                        $email = (string) $decoded['email'];
                    }

                    if (!empty($decoded['idnumber']) && $idnumber === null) {
                        $idnumber = (string) $decoded['idnumber'];
                    }

                    if (!empty($decoded['name'])) {
                        $name = (string) $decoded['name'];
                    }
                }
            } catch (\Throwable $e) {
                $idOrEmail = null;
                $name = null;
            }
        }

        if ($idOrEmail === null && str_contains($qr, ';')) {
            $segs = array_values(array_filter(array_map('trim', explode(';', $qr)), fn ($v) => $v !== ''));
            if (count($segs) >= 2) {
                $idnumber = $idnumber ?? $segs[0];
                $name = $name ?? $segs[1];
                $email = $email ?? ($segs[2] ?? null);

                if ($email !== null && $email !== '') {
                    $idOrEmail = $email;
                } else {
                    $idOrEmail = $idnumber;
                }
            }
        }

        if ($idOrEmail === null) {
            if ($email === null && preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $qr, $m)) {
                $email = (string) $m[0];
            }

            if ($idnumber === null && preg_match('/\b(?:idnumber|student\s*number|studentno|student\s*no|id)\s*[:=]\s*(\d+)\b/i', $qr, $m)) {
                $idnumber = (string) $m[1];
            }

            if ($name === null && preg_match('/\b(?:name|full\s*name|fullname)\s*[:=]\s*([^\n,;|]+)/i', $qr, $m)) {
                $name = trim((string) $m[1], " \t\n\r\0\x0B\"'");
            }

            if ($email !== null) {
                $idOrEmail = $email;
            } elseif ($idnumber !== null) {
                $idOrEmail = $idnumber;
            }
        }

        if (filter_var($qr, FILTER_VALIDATE_URL)) {
            try {
                $query = parse_url($qr, PHP_URL_QUERY);
                if ($query) {
                    parse_str($query, $params);
                    if (!empty($params['idnumber'])) {
                        $idnumber = (string) $params['idnumber'];
                        $idOrEmail = $idnumber;
                    } elseif (!empty($params['email'])) {
                        $email = (string) $params['email'];
                        $idOrEmail = $email;
                    }

                    if (!empty($params['email']) && $email === null) {
                        $email = (string) $params['email'];
                    }

                    if (!empty($params['idnumber']) && $idnumber === null) {
                        $idnumber = (string) $params['idnumber'];
                    }

                    if (!empty($params['name'])) {
                        $name = (string) $params['name'];
                    }
                }
            } catch (\Throwable $e) {
                $idOrEmail = null;
                $name = null;
            }
        }

        if ($idOrEmail === null) {
            $idOrEmail = $qr;
        }

        $loginField = filter_var($idOrEmail, FILTER_VALIDATE_EMAIL) ? 'email' : 'idnumber';
        $user = User::where($loginField, $idOrEmail)->first();

        if (!$user) {
            return response()->json([
                'ok' => false,
                'action' => 'register',
                'field' => $loginField,
                'value' => $idOrEmail,
                'idnumber' => $idnumber,
                'email' => $email,
                'name' => $name,
                'message' => 'Account not found. Please register.',
            ], 404);
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->is_admin) {
            AuditLogger::log(
                $request,
                $user->id,
                'SECURITY',
                'LOGIN',
                'Admin Login (QR)',
                'Admin logged in via QR.',
                'users',
                $user->id
            );
        }

        $redirect = $user->is_admin
            ? route('admin.dashboard')
            : route('home');

        return response()->json([
            'ok' => true,
            'redirect' => $redirect,
        ]);
    }
}
