<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Candidate;
use App\Models\Course;
use App\Models\Election;
use App\Models\ElectionParty;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use App\Observers\AdminAuditObserver;
use App\Services\Chatbot\AIProvider;
use App\Services\Chatbot\ChatbotAIRouter;
use App\Services\Chatbot\GeminiModelRouter;
use App\Services\Chatbot\GroqProvider;
use App\Services\Chatbot\MockAIProvider;
use App\Services\Chatbot\OpenAICompatibleProvider;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AIProvider::class, function () {
            return match (config('services.chatbot.provider')) {
                'router' => $this->app->make(ChatbotAIRouter::class),
                'groq' => $this->app->make(GroqProvider::class),
                'gemini' => $this->app->make(GeminiModelRouter::class),
                'openai' => $this->app->make(OpenAICompatibleProvider::class),
                'openai-compatible' => $this->app->make(OpenAICompatibleProvider::class),
                'mock' => $this->app->make(MockAIProvider::class),
                default => throw new InvalidArgumentException('Unsupported chatbot AI provider.'),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::component('search-box', \App\View\Components\SearchBox::class);
        Paginator::defaultView('components.pagination');

        Candidate::observe(AdminAuditObserver::class);
        ElectionParty::observe(AdminAuditObserver::class);
        Election::observe(AdminAuditObserver::class);
        Article::observe(AdminAuditObserver::class);
        Role::observe(AdminAuditObserver::class);
        Course::observe(AdminAuditObserver::class);
        Position::observe(AdminAuditObserver::class);

        View::composer(['layouts.admin-header', 'layouts.admin'], function ($view) {
            $candidateTotal = Candidate::query()->count();
            $candidatePending = Candidate::query()->where('status', 'Pending')->count();
            $candidateActive = Candidate::query()->where('status', 'Active')->count();
            $candidateArchived = Candidate::onlyTrashed()->count();

            $pendingVoterVerification = User::query()
                ->where('is_admin', false)
                ->whereNotNull('voter_verification_submitted_at')
                ->whereDoesntHave('verifiedVoter')
                ->count();

            $elections = Election::query()
                ->select(['id', 'name', 'start_date', 'end_date'])
                ->orderByDesc('id')
                ->get();

            $countdown = [
                'mode' => null,
                'targetIso' => null,
            ];

            $now = Carbon::now();
            $election = Election::query()->orderByDesc('id')->first();
            if ($election) {
                try {
                    $start = $election->start_date ? Carbon::parse($election->start_date) : null;
                    $end = $election->end_date ? Carbon::parse($election->end_date) : null;

                    if ($start && $now->lt($start)) {
                        $countdown['mode'] = 'start';
                        $countdown['targetIso'] = $start->toIso8601String();
                    } elseif ($end && $now->lt($end)) {
                        $countdown['mode'] = 'end';
                        $countdown['targetIso'] = $end->toIso8601String();
                    }
                } catch (\Throwable $e) {
                    $countdown = [
                        'mode' => null,
                        'targetIso' => null,
                    ];
                }
            }

            $view->with('adminWidget', [
                'candidates' => [
                    'total' => $candidateTotal,
                    'pending' => $candidatePending,
                    'active' => $candidateActive,
                    'archived' => $candidateArchived,
                ],
                'voters' => [
                    'pending_verification' => $pendingVoterVerification,
                ],
                'election' => [
                    'name' => $election->name ?? null,
                    'countdown' => $countdown,
                ],
                'elections' => $elections->map(function ($e) {
                    try {
                        $start = $e->start_date ? Carbon::parse($e->start_date)->toIso8601String() : null;
                    } catch (\Throwable $ex) {
                        $start = null;
                    }

                    try {
                        $end = $e->end_date ? Carbon::parse($e->end_date)->toIso8601String() : null;
                    } catch (\Throwable $ex) {
                        $end = null;
                    }

                    return [
                        'id' => $e->id,
                        'name' => $e->name,
                        'startIso' => $start,
                        'endIso' => $end,
                    ];
                })->values(),
            ]);
        });
    }
}
