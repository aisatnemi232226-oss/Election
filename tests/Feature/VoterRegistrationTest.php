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
            $table->timestamp('voter_verification_submitted_at')->nullable();
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

        Schema::create('acadyr', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
        });

        Schema::create('semesters', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
        });

        Schema::create('notifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('candidate', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->string('status')->default('Pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function test_guest_can_create_an_unverified_account_and_cannot_submit_voter_verification(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Create Account')
            ->assertSee('Creating an account does not verify voter eligibility.')
            ->assertSee('data-password-toggle="password"', false)
            ->assertSee('data-password-toggle="password_confirmation"', false)
            ->assertSee('aria-label="Show password"', false)
            ->assertSee('aria-label="Show confirmation password"', false);

        $this->post('/register', [
            'idnumber' => '20250001',
            'first_name' => 'Test',
            'middle_initial' => 'A.',
            'last_name' => 'Student',
            'email' => 'student@pcu.edu.ph',
            'password' => 'student-password',
            'password_confirmation' => 'student-password',
        ])->assertRedirect(route('register'))
            ->assertSessionHas('status', 'Your account has been created. Sign in to apply separately for voter verification.');

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Account Created!', false)
            ->assertSee('Continue to Login', false)
            ->assertSee('Swal.fire', false);

        $user = User::query()->where('email', 'student@pcu.edu.ph')->firstOrFail();
        $this->assertSame('Test A. Student', $user->name);
        $this->assertFalse($user->is_admin);
        $this->assertFalse($user->isVerifiedVoter());
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('student-password', $user->password));
        $this->assertDatabaseMissing('verified_voters', ['users_id' => $user->id]);

        $this->post('/register/verify', [
            'current_password' => 'student-password',
        ])->assertRedirect(route('login'));
    }

    public function test_account_registration_rejects_email_addresses_outside_the_pcu_domain(): void
    {
        $this->from('/register')
            ->post('/register', [
                'idnumber' => '20250011',
                'first_name' => 'Test',
                'middle_initial' => '',
                'last_name' => 'Student',
                'email' => 'student@example.test',
                'password' => 'student-password',
                'password_confirmation' => 'student-password',
            ])
            ->assertRedirect('/register')
            ->assertSessionHasErrors('email');

        $this->assertDatabaseMissing('users', ['idnumber' => '20250011']);
    }

    public function test_account_registration_accepts_case_insensitive_pcu_email_domain(): void
    {
        $this->post('/register', [
            'idnumber' => '20250012',
            'first_name' => 'Case',
            'middle_initial' => '',
            'last_name' => 'Student',
            'email' => 'student@PCU.EDU.PH',
            'password' => 'student-password',
            'password_confirmation' => 'student-password',
        ])->assertRedirect(route('register'));

        $this->assertDatabaseHas('users', ['email' => 'student@PCU.EDU.PH']);
    }

    public function test_registration_form_prefills_the_authenticated_users_details(): void
    {
        $user = User::create([
            'idnumber' => '20250002',
            'first_name' => 'Jamie',
            'middle_initial' => 'R.',
            'last_name' => 'Student',
            'name' => 'Jamie R. Student',
            'email' => 'jamie@example.test',
            'password' => 'existing-password',
        ]);

        $this->actingAs($user)
            ->get('/register/verify')
            ->assertOk()
            ->assertSee('<main class="relative z-10 container mx-auto p-4">', false)
            ->assertSee('class="inline-flex items-center gap-2 rounded-lg bg-white', false)
            ->assertSee('aria-label="Back to home"', false)
            ->assertDontSee('class="fixed left-4 top-4', false)
            ->assertSee('Submit your existing account for administrator verification')
            ->assertSee('value="20250002"', false)
            ->assertSee('id="toggle-current-password"', false)
            ->assertSee('aria-label="Show password"', false)
            ->assertSee('value="Jamie"', false)
            ->assertSee('value="R."', false)
            ->assertSee('value="Student"', false)
            ->assertSee('value="jamie@example.test"', false)
            ->assertSee('readonly', false)
            ->assertDontSee('name="password"', false);

        $this->actingAs($user)
            ->get('/register/options')
            ->assertOk()
            ->assertSee(route('register.verify'), false);
    }

    public function test_authenticated_voter_confirms_their_existing_password_instead_of_creating_an_account(): void
    {
        $user = User::create([
            'idnumber' => '20250003',
            'first_name' => 'Taylor',
            'last_name' => 'Student',
            'name' => 'Taylor Student',
            'email' => 'taylor@example.test',
            'password' => 'existing-password',
        ]);

        $this->actingAs($user)
            ->get('/register/verify')
            ->assertOk()
            ->assertSee('Re-enter Your Account Password')
            ->assertSee('name="current_password"', false)
            ->assertDontSee('name="password"', false);

        $this->actingAs($user)
            ->post('/register/verify', [
                'current_password' => 'existing-password',
            ])
            ->assertRedirect(route('register.verify'))
            ->assertSessionHas('status', 'Your existing account has been submitted for administrator verification. You can vote after an administrator verifies your student details.');

        $this->actingAs($user)
            ->get('/register/verify')
            ->assertOk()
            ->assertSee('id="verification-success-modal"', false)
            ->assertSee('role="dialog"', false)
            ->assertSee('aria-modal="true"', false)
            ->assertSee('Verification submitted')
            ->assertSee('What happens next?')
            ->assertSee('id="ok-verification-success"', false)
            ->assertSee('Your existing account has been submitted for administrator verification.', false);

        $this->assertSame(1, User::query()->where('email', 'taylor@example.test')->count());
        $this->assertNotNull($user->fresh()->voter_verification_submitted_at);
        $this->assertDatabaseMissing('verified_voters', ['users_id' => $user->id]);
        $this->assertTrue(
            User::query()
                ->where('is_admin', false)
                ->whereDoesntHave('verifiedVoter')
                ->whereKey($user->id)
                ->exists()
        );

        $this->actingAs($user)
            ->get('/register/verify')
            ->assertOk()
            ->assertSee('Pending Voter Application')
            ->assertSee('awaiting administrator review')
            ->assertDontSee('Your account details below come from your signed-in account.')
            ->assertDontSee('Submit for Verification')
            ->assertDontSee('id="current_password"', false)
            ->assertDontSee('id="verification-success-modal"', false);

        $this->actingAs($user)
            ->post('/register/verify', [
                'current_password' => 'existing-password',
            ])
            ->assertRedirect(route('register.verify'));
    }

    public function test_authenticated_voter_must_enter_their_correct_password(): void
    {
        $user = User::create([
            'idnumber' => '20250004',
            'first_name' => 'Morgan',
            'last_name' => 'Student',
            'name' => 'Morgan Student',
            'email' => 'morgan@example.test',
            'password' => 'existing-password',
        ]);

        $this->actingAs($user)
            ->from('/register/verify')
            ->post('/register/verify', [
                'current_password' => 'incorrect-password',
            ])
            ->assertRedirect('/register/verify')
            ->assertSessionHasErrors('current_password');
    }

    public function test_already_verified_voter_does_not_get_the_verification_form(): void
    {
        $user = User::create([
            'idnumber' => '20250005',
            'first_name' => 'Alex',
            'last_name' => 'Student',
            'name' => 'Alex Student',
            'email' => 'alex@example.test',
            'password' => 'existing-password',
        ]);
        $user->voter_verification_submitted_at = now();
        $user->save();
        DB::table('verified_voters')->insert([
            'users_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/register/verify')
            ->assertOk()
            ->assertSee('Your account is already verified to vote.')
            ->assertDontSee('name="current_password"', false);
    }

    public function test_admin_verification_notifies_student_and_student_can_mark_notification_read(): void
    {
        $student = User::create([
            'idnumber' => '20250006',
            'first_name' => 'Jordan',
            'last_name' => 'Student',
            'name' => 'Jordan Student',
            'email' => 'jordan@example.test',
            'password' => 'existing-password',
        ]);
        $admin = User::create([
            'idnumber' => '90000001',
            'name' => 'Administrator',
            'email' => 'admin@example.test',
            'password' => 'admin-password',
        ]);
        $admin->forceFill(['is_admin' => true])->save();
        DB::table('acadyr')->insert(['id' => 1, 'name' => '2026-2027']);
        DB::table('semesters')->insert(['id' => 1, 'name' => '1st Semester']);

        $this->actingAs($admin)
            ->postJson(route('admin.verified-voters.store'), [
                'user_id' => $student->id,
                'acadyr_id' => 1,
                'sem_id' => 1,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->actingAs($student)
            ->getJson(route('student.notifications.index'))
            ->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonPath('notifications.0.title', 'Voter verification approved')
            ->assertJsonPath('notifications.0.message', 'Your account has been verified. You can now vote in the election.');

        $this->actingAs($student)
            ->postJson(route('student.notifications.read'))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->actingAs($student)
            ->getJson(route('student.notifications.index'))
            ->assertOk()
            ->assertJsonPath('unread_count', 0);
    }

    public function test_admin_notifications_offer_actions_for_submitted_voter_applications_only(): void
    {
        $student = User::create([
            'idnumber' => '20250013',
            'name' => 'Pending Student',
            'email' => 'pending@example.test',
            'password' => 'existing-password',
        ]);
        $student->forceFill(['voter_verification_submitted_at' => now()])->save();
        User::create([
            'idnumber' => '20250014',
            'name' => 'Not Submitted Student',
            'email' => 'not-submitted@example.test',
            'password' => 'existing-password',
        ]);
        $admin = User::create([
            'idnumber' => '90000002',
            'name' => 'Administrator',
            'email' => 'admin-notifications@example.test',
            'password' => 'admin-password',
        ]);
        $admin->forceFill(['is_admin' => true])->save();
        DB::table('acadyr')->insert(['id' => 1, 'name' => '2026-2027']);
        DB::table('semesters')->insert(['id' => 1, 'name' => '1st Semester']);

        $this->actingAs($admin)
            ->getJson(route('admin.notifications.index'))
            ->assertOk()
            ->assertJsonPath('counts.voters_pending_verification', 1)
            ->assertJsonPath('voters.0.id', $student->id)
            ->assertJsonPath('academic_years.0.name', '2026-2027')
            ->assertJsonPath('semesters.0.name', '1st Semester');

        $this->actingAs($admin)
            ->postJson(route('admin.verified-voters.reject', $student))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertNull($student->fresh()->voter_verification_submitted_at);

        $this->actingAs($admin)
            ->getJson(route('admin.notifications.index'))
            ->assertOk()
            ->assertJsonPath('counts.voters_pending_verification', 0)
            ->assertJsonCount(0, 'voters');
    }
}
