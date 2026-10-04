<?php

namespace App\Services;

use App\Models\Election;
use App\Models\ElectionEventSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ElectionWidgetService
{
    private const CACHE_SECONDS = 8;

    public function currentElection(): ?Election
    {
        return Election::query()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first();
    }

    public function resultsVisible(?Election $election, ?Carbon $now = null): bool
    {
        if (! $election) {
            return false;
        }

        return match ($election->result_visibility) {
            'live_candidate_results' => true,
            'after_close' => $this->electionHasClosed($election, $now ?? now()),
            default => false,
        };
    }

    public function forget(?int $electionId): void
    {
        if ($electionId) {
            Cache::forget($this->cacheKey($electionId));
        }
    }

    public function payload(?int $electionId = null): array
    {
        $now = now();
        $election = $electionId
            ? Election::query()->find($electionId)
            : $this->currentElection();
        $elections = Election::query()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get(['id', 'name', 'start_date'])
            ->map(fn (Election $option): array => [
                'id' => (int) $option->id,
                'name' => (string) $option->name,
                'label' => (string) $option->name . ($option->start_date ? ' · ' . $option->start_date->format('Y') : ''),
            ])
            ->all();

        if (! $election) {
            return [
                'election' => null,
                'elections' => $elections,
                'status' => 'unavailable',
                'status_label' => 'Election information is not available',
                'server_time' => $now->toIso8601String(),
                'countdown' => ['target_time' => null, 'target_label' => null],
                'timeline' => [],
                'upcoming_events' => $this->upcomingEvents($now),
                'statistics' => null,
                'results' => ['visible' => false],
                'freshness' => ['generated_at' => $now->toIso8601String(), 'cache_age_seconds' => 0],
            ];
        }

        $cache = Cache::remember(
            $this->cacheKey((int) $election->id),
            self::CACHE_SECONDS,
            fn (): array => $this->buildCachedData($election, $now)
        );

        $generatedAt = Carbon::parse($cache['generated_at']);

        $cache['server_time'] = $now->toIso8601String();
        $cache['elections'] = $elections;
        $cache['freshness'] = [
            'generated_at' => $generatedAt->toIso8601String(),
            'cache_age_seconds' => max(0, $generatedAt->diffInSeconds($now)),
        ];
        unset($cache['generated_at']);

        return $cache;
    }

    private function buildCachedData(Election $election, Carbon $now): array
    {
        $schedule = $this->schedule($election);
        $status = $this->electionStatus($election, $schedule, $now);
        $statistics = $this->statistics((int) $election->id);

        return [
            'election' => [
                'id' => (int) $election->id,
                'name' => (string) $election->name,
            ],
            'status' => $status['value'],
            'status_label' => $status['label'],
            'server_time' => $now->toIso8601String(),
            'countdown' => [
                'target_time' => $status['target']?->toIso8601String(),
                'target_label' => $status['target_label'],
            ],
            'timeline' => $this->timeline($schedule, $now),
            'upcoming_events' => $this->upcomingEvents($now),
            'statistics' => $statistics,
            'results' => ['visible' => $this->resultsVisible($election, $now)],
            'generated_at' => $now->toIso8601String(),
        ];
    }

    private function statistics(int $electionId): array
    {
        $eligibleVoters = DB::table('verified_voters')
            ->join('users', 'users.id', '=', 'verified_voters.users_id')
            ->whereNull('verified_voters.deleted_at')
            ->where('users.is_admin', false)
            ->distinct()
            ->count('verified_voters.users_id');

        $votersParticipated = DB::table('position_votes')
            ->join('verified_voters', 'verified_voters.users_id', '=', 'position_votes.user_id')
            ->join('users', 'users.id', '=', 'position_votes.user_id')
            ->where('election_id', $electionId)
            ->whereNull('verified_voters.deleted_at')
            ->where('users.is_admin', false)
            ->distinct()
            ->count('position_votes.user_id');

        return [
            'eligible_voters' => (int) $eligibleVoters,
            'voters_participated' => (int) $votersParticipated,
            'turnout_percentage' => $eligibleVoters > 0
                ? round(($votersParticipated / $eligibleVoters) * 100, 2)
                : 0.0,
        ];
    }

    private function schedule(Election $election): array
    {
        $timezone = config('app.timezone');

        return array_values(array_filter([
            $this->period('Registration', $election->reg_start, $election->reg_end, $timezone),
            $this->period('Campaign', $election->campaign_start, $election->campaign_end, $timezone),
            $this->period('Voting', $election->start_date, $election->end_date, $timezone),
            $this->period('Results announcement', $election->result_date, null, $timezone),
        ]));
    }

    private function period(string $title, mixed $start, mixed $end, string $timezone): ?array
    {
        if (! $start) {
            return null;
        }

        $startsAt = Carbon::parse($start, $timezone)->startOfDay();
        $endsAt = $end ? Carbon::parse($end, $timezone)->endOfDay() : $startsAt->copy();

        return [
            'title' => $title,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ];
    }

    private function timeline(array $schedule, Carbon $now): array
    {
        return array_map(function (array $period) use ($now): array {
            $state = $now->gt($period['ends_at'])
                ? 'completed'
                : ($now->gte($period['starts_at']) ? 'current' : 'upcoming');

            return [
                'title' => $period['title'],
                'starts_at' => $period['starts_at']->toIso8601String(),
                'ends_at' => $period['ends_at']->toIso8601String(),
                'starts_label' => $period['starts_at']->format('F j, Y'),
                'ends_label' => $period['ends_at']->format('F j, Y'),
                'state' => $state,
            ];
        }, $schedule);
    }

    private function upcomingEvents(Carbon $now): array
    {
        $settings = ElectionEventSetting::query()->first();
        if (! $settings) {
            return [];
        }

        $events = [];
        for ($slot = 1; $slot <= 3; $slot++) {
            $title = trim((string) $settings->{"event_{$slot}_title"});
            $date = trim((string) $settings->{"event_{$slot}_date"});
            if ($title === '' || $date === '') {
                continue;
            }

            try {
                $startsAt = Carbon::parse($date, config('app.timezone'));
            } catch (\Exception) {
                continue;
            }

            $hasTime = preg_match('/\b\d{1,2}:\d{2}\b/', $date) === 1;
            if (($hasTime && $startsAt->lt($now)) || (! $hasTime && $startsAt->copy()->endOfDay()->lt($now))) {
                continue;
            }

            $events[] = [
                'title' => $title,
                'description' => (string) $settings->{"event_{$slot}_description"},
                'starts_at' => $startsAt->toIso8601String(),
                'display_date' => $startsAt->format($hasTime ? 'F j, Y · g:i A' : 'F j, Y'),
            ];
        }

        usort(
            $events,
            fn (array $left, array $right): int => Carbon::parse($left['starts_at'])->getTimestamp()
                <=> Carbon::parse($right['starts_at'])->getTimestamp()
        );

        return $events;
    }

    private function electionStatus(Election $election, array $schedule, Carbon $now): array
    {
        if ($schedule === []) {
            return [
                'value' => 'unavailable',
                'label' => 'Election dates have not been published',
                'target' => null,
                'target_label' => null,
            ];
        }

        $target = null;
        $targetLabel = null;
        $current = null;
        $next = null;

        foreach ($schedule as $period) {
            if ($now->gte($period['starts_at']) && $now->lte($period['ends_at'])) {
                $current = $period;
            } elseif ($period['starts_at']->gt($now) && (! $next || $period['starts_at']->lt($next['starts_at']))) {
                $next = $period;
            }
        }

        if ($current) {
            $target = $current['ends_at'];
            $targetLabel = $current['title'] === 'Voting' ? 'Voting closes' : $current['title'] . ' ends';
            if ($current['title'] === 'Voting') {
                return ['value' => 'voting_open', 'label' => 'Voting is currently open', 'target' => $target, 'target_label' => $targetLabel];
            }
            if ($current['title'] === 'Campaign') {
                return ['value' => 'campaign', 'label' => 'The campaign period is underway', 'target' => $target, 'target_label' => $targetLabel];
            }
            if ($current['title'] === 'Registration') {
                return ['value' => 'registration_open', 'label' => 'Registration is currently open', 'target' => $target, 'target_label' => $targetLabel];
            }
        }

        if ($next) {
            $target = $next['starts_at'];
            $targetLabel = $next['title'] . ' begins';
            return ['value' => 'upcoming', 'label' => 'The next election phase is upcoming', 'target' => $target, 'target_label' => $targetLabel];
        }

        $resultDate = $election->result_date
            ? Carbon::parse($election->result_date, config('app.timezone'))->startOfDay()
            : null;
        if ($resultDate && $now->lt($resultDate)) {
            return ['value' => 'counting', 'label' => 'Voting has ended; results are pending', 'target' => $resultDate, 'target_label' => 'Results are expected'];
        }

        if ($this->resultsVisible($election, $now)) {
            return ['value' => 'results_available', 'label' => 'Official election results are available', 'target' => null, 'target_label' => null];
        }

        return ['value' => 'completed', 'label' => 'Voting has ended', 'target' => null, 'target_label' => null];
    }

    private function electionHasClosed(Election $election, Carbon $now): bool
    {
        if (! $election->end_date) {
            return false;
        }

        return $now->gt(Carbon::parse($election->end_date, config('app.timezone'))->endOfDay());
    }

    private function cacheKey(int $electionId): string
    {
        return "election-widget:{$electionId}";
    }
}
