<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Election;
use App\Services\ElectionWidgetService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ElectionWidgetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.timezone' => 'Asia/Manila',
            'app.url' => 'http://localhost',
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => false,
        ]);
        $this->app['url']->forceRootUrl('http://localhost');
        DB::purge('sqlite');
        Cache::store()->flush();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('idnumber')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->timestamps();
        });

        Schema::create('election', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('reg_start')->nullable();
            $table->date('reg_end')->nullable();
            $table->date('campaign_start')->nullable();
            $table->date('campaign_end')->nullable();
            $table->date('result_date')->nullable();
            $table->string('result_visibility')->default('hidden');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('verified_voters', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('yrlvl_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('candidate', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('election_id')->nullable();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->string('status')->nullable();
            $table->string('profile_img')->nullable();
            $table->string('banner_img')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('position_votes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('election_id')->nullable();
            $table->unsignedBigInteger('position_id');
            $table->unsignedBigInteger('candidate_id');
            $table->timestamps();
            $table->unique(['user_id', 'election_id', 'position_id'], 'position_votes_user_election_position_unique');
        });

        Schema::create('positions', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->softDeletes();
        });

        Schema::create('election_articles', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('articles_id')->nullable();
            $table->boolean('show_status')->default(true);
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('articles', function (Blueprint $table): void {
            $table->id();
            $table->softDeletes();
        });

        Schema::create('election_event_settings', function (Blueprint $table): void {
            $table->id();
            foreach (range(1, 3) as $slot) {
                $table->string("event_{$slot}_title")->nullable();
                $table->string("event_{$slot}_date")->nullable();
                $table->text("event_{$slot}_description")->nullable();
            }
        });
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_widget_returns_the_selected_election_schedule_and_election_scoped_turnout(): void
    {
        $this->travelTo(Carbon::parse('2026-10-04 12:00:00', 'Asia/Manila'));
        $this->createElection(1, [
            'start_date' => '2026-10-04',
            'end_date' => '2026-10-05',
        ]);
        $this->createElection(2, [
            'start_date' => '2026-11-04',
            'end_date' => '2026-11-05',
        ]);

        $firstVoter = $this->createVoter('first@example.test');
        $secondVoter = $this->createVoter('second@example.test');
        $admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'is_admin' => true,
        ]);
        DB::table('verified_voters')->insert([
            'users_id' => $admin->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('position_votes')->insert([
            [
                'user_id' => $firstVoter->id,
                'election_id' => 1,
                'position_id' => 1,
                'candidate_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $secondVoter->id,
                'election_id' => 2,
                'position_id' => 1,
                'candidate_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $admin->id,
                'election_id' => 1,
                'position_id' => 1,
                'candidate_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $admin->id,
                'election_id' => 2,
                'position_id' => 2,
                'candidate_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        DB::table('election_event_settings')->insert([
            'event_1_title' => 'Public forum',
            'event_1_date' => '2026-10-06',
            'event_1_description' => 'Meet the candidates',
        ]);

        $response = $this->getJson('/api/election/widget');
        $this->assertSame(200, $response->status(), $response->getContent());
        $response
            ->assertOk()
            ->assertJsonPath('election.name', 'Election 2')
            ->assertJsonPath('status', 'upcoming')
            ->assertJsonPath('statistics.eligible_voters', 2)
            ->assertJsonPath('statistics.voters_participated', 1)
            ->assertJsonPath('statistics.turnout_percentage', 50)
            ->assertJsonPath('results.visible', false)
            ->assertJsonPath('upcoming_events.0.title', 'Public forum');
    }

    public function test_home_carousel_uses_active_unlinked_profiles_when_the_current_election_has_no_candidates(): void
    {
        $this->createElection(1);
        DB::table('positions')->insert([
            'id' => 1,
            'name' => 'President',
        ]);
        DB::table('candidate')->insert([
            [
                'id' => 1,
                'election_id' => null,
                'name' => 'Legacy Candidate One',
                'position_id' => 1,
                'status' => 'Active',
            ],
            [
                'id' => 2,
                'election_id' => null,
                'name' => 'Legacy Candidate Two',
                'position_id' => 1,
                'status' => 'Active',
            ],
            [
                'id' => 3,
                'election_id' => null,
                'name' => 'Pending Candidate',
                'position_id' => 1,
                'status' => 'Pending',
            ],
        ]);

        $response = $this->get('/')->assertOk();
        $html = $response->getContent();

        $response
            ->assertSee('Candidate profiles')
            ->assertSee('Legacy Candidate One')
            ->assertSee('Legacy Candidate Two')
            ->assertDontSee('Pending Candidate')
            ->assertSee('data-election-widget-open', false)
            ->assertSee('id="election-widget-panel"', false)
            ->assertSee('data-election-widget-close', false)
            ->assertSee('data-candidate-next', false)
            ->assertDontSee('data-tallies-url=', false);
        $this->assertSame(2, substr_count($html, 'class="candidate-slide"'));
        $this->assertMatchesRegularExpression('/<article\s+class="election-widget election-widget-panel"[\s\S]*?\shidden>/', $html);
        $this->assertStringContainsString('candidateTimer = window.setInterval', $html);
    }

    public function test_candidate_tallies_and_result_pages_do_not_reveal_unpublished_totals(): void
    {
        $this->travelTo(Carbon::parse('2026-10-04 12:00:00', 'Asia/Manila'));
        $this->createElection(1, [
            'start_date' => '2026-10-04',
            'end_date' => '2026-10-05',
            'result_visibility' => 'hidden',
        ]);
        DB::table('positions')->insert(['id' => 1, 'name' => 'President']);
        DB::table('candidate')->insert([
            'id' => 1,
            'election_id' => 1,
            'name' => 'Candidate One',
            'position_id' => 1,
            'status' => 'Active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $voter = $this->createVoter('voter@example.test');
        DB::table('position_votes')->insert([
            'user_id' => $voter->id,
            'election_id' => 1,
            'position_id' => 1,
            'candidate_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson('/candidate-tallies');
        $this->assertSame(200, $response->status(), $response->getContent());
        $response
            ->assertOk()
            ->assertExactJson(['results' => ['visible' => false]]);

        $this->get('/result')
            ->assertOk()
            ->assertSee('Official results are not available yet.')
            ->assertSee('NOT PUBLISHED')
            ->assertDontSee('Candidate One');
        $this->get('/resultTally')
            ->assertOk()
            ->assertSee('Candidate results have not been published for this election.')
            ->assertDontSee('Candidate One');
    }

    public function test_after_close_results_become_visible_only_after_the_voting_day_ends(): void
    {
        $this->createElection(1, [
            'start_date' => '2026-10-04',
            'end_date' => '2026-10-05',
            'result_visibility' => 'after_close',
        ]);
        $election = Election::query()->findOrFail(1);
        $widget = app(ElectionWidgetService::class);

        $this->assertFalse($widget->resultsVisible(
            $election,
            Carbon::parse('2026-10-05 23:59:59', 'Asia/Manila')
        ));
        $this->assertTrue($widget->resultsVisible(
            $election,
            Carbon::parse('2026-10-06 00:00:00', 'Asia/Manila')
        ));
    }

    public function test_ballots_are_rejected_outside_the_configured_voting_window(): void
    {
        $election = $this->createElection(1, [
            'start_date' => '2026-10-05',
            'end_date' => '2026-10-06',
        ]);
        DB::table('positions')->insert(['id' => 1, 'name' => 'President']);
        DB::table('candidate')->insert([
            'id' => 1,
            'election_id' => $election->id,
            'name' => 'Candidate One',
            'position_id' => 1,
            'status' => 'Active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $voter = $this->createVoter('voter@example.test');

        $this->travelTo(Carbon::parse('2026-10-04 23:59:00', 'Asia/Manila'));
        $response = $this->actingAs($voter)
            ->from('/election-details')
            ->post('/vote/cast', ['candidate_id' => 1]);
        $this->assertSame(302, $response->status(), $response->getContent());
        $response->assertRedirect('/election-details')
            ->assertSessionHas('error', 'Voting is not currently open for this election.');
        $this->assertDatabaseCount('position_votes', 0);

        $this->travelTo(Carbon::parse('2026-10-05 00:00:00', 'Asia/Manila'));
        $this->actingAs($voter)
            ->from('/election-details')
            ->post('/vote/cast', ['candidate_id' => 1])
            ->assertRedirect('/election-details')
            ->assertSessionHas('success', 'Your vote has been recorded.');
        $this->assertDatabaseCount('position_votes', 1);

        $anotherVoter = $this->createVoter('another@example.test');
        $this->travelTo(Carbon::parse('2026-10-07 00:00:00', 'Asia/Manila'));
        $this->actingAs($anotherVoter)
            ->from('/election-details')
            ->post('/vote/cast', ['candidate_id' => 1])
            ->assertRedirect('/election-details')
            ->assertSessionHas('error', 'Voting is not currently open for this election.');
        $this->assertDatabaseCount('position_votes', 1);
    }

    private function createElection(int $id, array $overrides = []): object
    {
        $attributes = array_merge([
            'id' => $id,
            'name' => "Election {$id}",
            'result_visibility' => 'hidden',
            'created_at' => now()->addSeconds($id),
        ], $overrides);
        DB::table('election')->insert($attributes);

        return (object) $attributes;
    }

    private function createVoter(string $email): User
    {
        $user = User::query()->create([
            'name' => 'Test Voter',
            'email' => $email,
            'password' => 'password',
            'is_admin' => false,
        ]);
        DB::table('verified_voters')->insert([
            'users_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $user;
    }
}
