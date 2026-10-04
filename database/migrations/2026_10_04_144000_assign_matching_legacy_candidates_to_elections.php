<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $electionIdsByRole = [];
        foreach (DB::table('election')->whereNull('deleted_at')->get(['id', 'name']) as $election) {
            $role = $this->roleForElection((string) $election->name);
            if ($role !== null) {
                $electionIdsByRole[$role][] = (int) $election->id;
            }
        }

        $positionRoles = [];
        foreach (DB::table('positions')->whereNull('deleted_at')->get(['id', 'name']) as $position) {
            $role = $this->roleForPosition((string) $position->name);
            if ($role !== null) {
                $positionRoles[(int) $position->id] = $role;
            }
        }

        $candidates = DB::table('candidate')
            ->where('status', 'Active')
            ->whereNull('election_id')
            ->whereNull('deleted_at')
            ->whereNotNull('position_id')
            ->get(['id', 'position_id']);

        foreach ($candidates as $candidate) {
            $role = $positionRoles[(int) $candidate->position_id] ?? null;
            $matchingElectionIds = $role === null ? [] : ($electionIdsByRole[$role] ?? []);

            if (count($matchingElectionIds) === 1) {
                DB::table('candidate')
                    ->where('id', $candidate->id)
                    ->whereNull('election_id')
                    ->update(['election_id' => $matchingElectionIds[0]]);
            }
        }
    }

    public function down(): void
    {
        // Keep assignments intact so rollback cannot erase later admin changes.
    }

    private function roleForElection(string $name): ?string
    {
        return match ($this->normalize($name)) {
            'presidential' => 'presidential',
            'vicepresident', 'vice' => 'vice',
            default => null,
        };
    }

    private function roleForPosition(string $name): ?string
    {
        return match ($this->normalize($name)) {
            'presidential' => 'presidential',
            'vice', 'vicepresident' => 'vice',
            default => null,
        };
    }

    private function normalize(string $name): string
    {
        return preg_replace('/[^a-z0-9]/', '', mb_strtolower(trim($name))) ?? '';
    }
};
