<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterElectionController extends Controller
{
    public function show(Request $request)
    {
        $today = now()->toDateString();

        $user = $request->user();

        $elections = Election::query()
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->get();

        return view('voter.registerElection', [
            'elections' => $elections,
            'prefillCredential' => $request->query('credential', ''),
            'prefillElectionId' => $request->query('election_id', ''),
            'authUser' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'election_id' => 'required|integer|exists:election,id',
            'credential' => 'nullable|string',
            'idnumber' => 'nullable|string',
            'name' => 'nullable|string',
            'email' => 'nullable|string',
        ]);

        $election = Election::query()->where('id', (int) $validated['election_id'])->whereNull('deleted_at')->first();
        if (!$election) {
            return back()->withErrors(['election_id' => 'Election not found.'])->withInput();
        }

        $today = now()->toDateString();
        $regStart = $election->reg_start ? $election->reg_start->toDateString() : null;
        $regEnd = $election->reg_end ? $election->reg_end->toDateString() : null;

        $isRegOpen = ($regStart === null || $regStart <= $today) && ($regEnd === null || $regEnd >= $today);
        if (!$isRegOpen) {
            return back()->withErrors(['election_id' => 'Registration is closed for the selected election.'])->withInput();
        }

        $user = $request->user();

        if (!$user) {
            $idnumber = trim((string) ($validated['idnumber'] ?? ''));
            $email = trim((string) ($validated['email'] ?? ''));
            $name = trim((string) ($validated['name'] ?? ''));
            $credential = trim((string) ($validated['credential'] ?? ''));

            if ($email !== '') {
                $user = User::query()->where('email', $email)->first();
            } elseif ($idnumber !== '') {
                $user = User::query()->where('idnumber', preg_replace('/[^0-9]/', '', $idnumber))->first();
            } elseif ($credential !== '') {
                $field = filter_var($credential, FILTER_VALIDATE_EMAIL) ? 'email' : 'idnumber';
                $value = $field === 'idnumber' ? preg_replace('/[^0-9]/', '', $credential) : $credential;
                $user = User::query()->where($field, $value)->first();
            }

            if (!$user) {
                $redirectTo = route('registerElection', ['election_id' => (int) $validated['election_id']]);

                $params = [
                    'redirect_to' => $redirectTo,
                ];

                if ($idnumber !== '') {
                    $params['idnumber'] = preg_replace('/[^0-9]/', '', $idnumber);
                }

                if ($email !== '') {
                    $params['email'] = $email;
                }

                if ($name !== '') {
                    $params['name'] = $name;
                }

                if (empty($params['idnumber']) && empty($params['email']) && $credential !== '') {
                    if (filter_var($credential, FILTER_VALIDATE_EMAIL)) {
                        $params['email'] = $credential;
                    } else {
                        $params['idnumber'] = preg_replace('/[^0-9]/', '', $credential);
                    }
                }

                return redirect()->route('register', $params);
            }
        }

        $exists = DB::table('register')
            ->where('user_id', $user->id)
            ->where('election_id', (int) $validated['election_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['election_id' => 'You are already registered for this election.'])->withInput();
        }

        DB::table('register')->insert([
            'user_id' => $user->id,
            'election_id' => (int) $validated['election_id'],
            'status' => 'Registered',
            'created_at' => now(),
        ]);

        return redirect()->route('registerElection')->with('success', 'Successfully registered for the election.');
    }
}
