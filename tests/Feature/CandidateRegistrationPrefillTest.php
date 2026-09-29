<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CandidateRegistrationPrefillTest extends TestCase
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
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('idnumber')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->string('coursecode')->nullable();
            $table->softDeletes();
        });

        Schema::create('electionparty', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->softDeletes();
        });

        Schema::create('positions', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->softDeletes();
        });

        Schema::create('candidate', function (Blueprint $table): void {
            $table->id();
            $table->string('id_number')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function test_authenticated_candidate_registration_prefills_credential_with_student_number(): void
    {
        $user = User::query()->create([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
            'idnumber' => '20250001',
        ]);

        $this->actingAs($user)
            ->get('/candidate/register')
            ->assertOk()
            ->assertSee('id="credential" type="text" placeholder="Scan QR or type student number/email" value="20250001" readonly', false);
    }

    public function test_authenticated_candidate_registration_uses_email_when_student_number_is_missing(): void
    {
        $user = User::query()->create([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
            'idnumber' => '',
        ]);

        $this->actingAs($user)
            ->get('/candidate/register')
            ->assertOk()
            ->assertSee('id="credential" type="text" placeholder="Scan QR or type student number/email" value="student@example.test" readonly', false);
    }
}
