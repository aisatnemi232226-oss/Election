<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class VoterRegistrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.url' => 'http://localhost',
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => false,
        ]);
        $this->app['url']->forceRootUrl('http://localhost');
        DB::purge('sqlite');

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('middle_initial')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('idnumber')->unique();
            $table->string('login_qr_hash')->nullable();
            $table->string('password_recovery_qr_hash')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->timestamps();
        });

        Schema::create('verified_voters', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('users_id')->nullable();
            $table->unsignedBigInteger('acadyr_id')->nullable();
            $table->unsignedBigInteger('sem_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function test_public_registration_creates_an_unverified_voter_for_admin_review(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Voter Registration')
            ->assertSee('An administrator must verify your voter eligibility before you can vote.');

        $this->post('/register', [
            'idnumber' => '20250001',
            'first_name' => 'Test',
            'middle_initial' => 'A.',
            'last_name' => 'Student',
            'email' => 'student@example.test',
            'password' => 'student-password',
        ])->assertRedirect(route('account.qr-codes'));

        $user = User::query()->where('idnumber', '20250001')->firstOrFail();

        $this->assertDatabaseHas('users', [
            'idnumber' => '20250001',
            'name' => 'Test A. Student',
            'email' => 'student@example.test',
            'is_admin' => false,
        ]);
        $this->assertDatabaseMissing('verified_voters', [
            'users_id' => $user->id,
        ]);
        $this->assertTrue(
            User::query()
                ->where('is_admin', false)
                ->whereDoesntHave('verifiedVoter')
                ->whereKey($user->id)
                ->exists()
        );

        $this->get(route('account.qr-codes'))
            ->assertOk()
            ->assertSee('awaiting administrator verification')
            ->assertSee('Download login QR');
    }
}
