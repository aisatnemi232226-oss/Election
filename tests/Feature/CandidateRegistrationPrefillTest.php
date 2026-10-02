<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
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
            $table->timestamp('voter_verification_submitted_at')->nullable();
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
            $table->unsignedBigInteger('users_id')->nullable();
            $table->string('name')->nullable();
            $table->string('id_number')->nullable();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->unsignedBigInteger('electionparty_id')->nullable();
            $table->text('platforms')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->nullable();
            $table->string('profile_img')->nullable();
            $table->string('banner_img')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('verified_voters', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('users_id');
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
            ->assertSee('<main class="relative z-10 container mx-auto w-full flex-1 p-4">', false)
            ->assertSee('class="inline-flex items-center gap-2 rounded-lg bg-white', false)
            ->assertSee('aria-label="Back to home"', false)
            ->assertDontSee('class="fixed left-4 top-4', false)
            ->assertSee('<body class="relative flex min-h-screen flex-col bg-gray-100">', false)
            ->assertSee('<main class="relative z-10 container mx-auto w-full flex-1 p-4">', false)
            ->assertSee('mt-auto border-t border-blue-500/30 bg-blue-700', false)
            ->assertDontSee('Pending Voter Verification')
            ->assertSee('id="credential" type="text" placeholder="Scan QR or type student number/email" value="20250001" readonly', false);
    }

    public function test_candidate_registration_does_not_show_candidate_pending_notice_for_voter_verification_only(): void
    {
        $user = User::query()->create([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
            'idnumber' => '20250001',
        ]);
        $user->forceFill(['voter_verification_submitted_at' => now()])->save();

        $this->actingAs($user)
            ->get('/candidate/register')
            ->assertOk()
            ->assertDontSee('Pending Candidate Application')
            ->assertDontSee('Your candidate application has been submitted')
            ->assertSee('name="position_id"', false)
            ->assertSee('name="course_id"', false);
    }

    public function test_candidate_registration_hides_general_notice_when_application_is_already_pending(): void
    {
        $user = User::query()->create([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
            'idnumber' => '20250001',
        ]);
        DB::table('candidate')->insert([
            'id_number' => '20250001',
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        $this->actingAs($user)
            ->get('/candidate/register')
            ->assertOk()
            ->assertDontSee('You have already submitted your application.')
            ->assertDontSee('After submitting your application, it will be reviewed by the admin')
            ->assertSee('Pending Candidate Application')
            ->assertSee('Your candidate application has been submitted and is awaiting administrator review.');
    }

    public function test_candidate_registration_hides_pending_indicator_for_verified_voter(): void
    {
        $user = User::query()->create([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
            'idnumber' => '20250001',
        ]);
        $user->forceFill(['voter_verification_submitted_at' => now()])->save();
        DB::table('verified_voters')->insert([
            'users_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/candidate/register')
            ->assertOk()
            ->assertDontSee('Pending Voter Verification');
    }

    public function test_candidate_registration_shows_accessible_success_confirmation_dialog(): void
    {
        $this->withSession(['success' => 'Application submitted. Please wait for admin approval.'])
            ->get('/candidate/register')
            ->assertOk()
            ->assertSee('id="success-modal"', false)
            ->assertSee('role="dialog"', false)
            ->assertSee('aria-modal="true"', false)
            ->assertSee('id="success-inline-notice"', false)
            ->assertSee('id="success-inline-notice" class="candidate-success-inline" role="status" tabindex="-1" hidden', false)
            ->assertDontSee('Pending Candidate Application')
            ->assertSee('Application submitted')
            ->assertSee('success-modal-icon', false)
            ->assertSee('What happens next?')
            ->assertSee('id="ok-success-modal"', false);
    }

    public function test_invalid_candidate_submission_shows_timed_error_and_creates_no_pending_application(): void
    {
        Storage::fake('public');

        $user = User::query()->create([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
            'idnumber' => '20250001',
        ]);
        DB::table('courses')->insert(['name' => 'Test Course', 'coursecode' => 'TEST']);
        DB::table('positions')->insert(['name' => 'President']);

        $response = $this->actingAs($user)
            ->from('/candidate/register')
            ->post('/candidate/register', [
                'idnumber' => '20250001',
                'name' => 'Test Student',
                'email' => 'student@example.test',
                'course_id' => 1,
                'position_id' => 1,
                'description' => 'Test platform description',
                'platforms' => 'Test platform',
                'profile_img' => UploadedFile::fake()->image('profile.jpg')->size(10241),
                'banner_img' => UploadedFile::fake()->image('banner.jpg')->size(10241),
            ]);

        $response->assertRedirect('/candidate/register')
            ->assertSessionHasErrors(['profile_img', 'banner_img']);

        $this->assertDatabaseCount('candidate', 0);
        $this->assertNull($user->fresh()->voter_verification_submitted_at);
        Storage::disk('public')->assertDirectoryEmpty('upload/profile-picture');
        Storage::disk('public')->assertDirectoryEmpty('upload/banner');

        $this->get('/candidate/register')
            ->assertOk()
            ->assertSee('id="candidate-error-toast"', false)
            ->assertSee('popover="manual"', false)
            ->assertSee('Please fix the following errors')
            ->assertSee('5000', false)
            ->assertDontSee('Pending Voter Verification')
            ->assertDontSee('Application Submitted');
    }

    public function test_candidate_registration_accepts_images_up_to_10_mb_each(): void
    {
        Storage::fake('public');
        $user = User::query()->create([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
            'idnumber' => '20250001',
        ]);
        DB::table('courses')->insert(['name' => 'Test Course', 'coursecode' => 'TEST']);
        DB::table('positions')->insert(['name' => 'President']);

        $this->actingAs($user)
            ->post('/candidate/register', [
                'idnumber' => '20250001',
                'name' => 'Test Student',
                'email' => 'student@example.test',
                'course_id' => 1,
                'position_id' => 1,
                'description' => 'Test platform description',
                'platforms' => 'Test platform',
                'profile_img' => UploadedFile::fake()->image('profile.jpg')->size(10240),
                'banner_img' => UploadedFile::fake()->image('banner.jpg')->size(10240),
            ])
            ->assertRedirect(route('candidate.register'))
            ->assertSessionHas('success', 'Application submitted. Please wait for admin approval.');

        $this->assertDatabaseHas('candidate', [
            'id_number' => '20250001',
            'status' => 'Pending',
        ]);
        Storage::disk('public')->assertExists('upload/profile-picture');
        Storage::disk('public')->assertExists('upload/banner');
    }

    public function test_existing_application_error_does_not_store_uploaded_files_or_create_another_record(): void
    {
        Storage::fake('public');
        $user = User::query()->create([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
            'idnumber' => '20250001',
        ]);
        DB::table('courses')->insert(['name' => 'Test Course', 'coursecode' => 'TEST']);
        DB::table('positions')->insert(['name' => 'President']);
        DB::table('candidate')->insert([
            'id_number' => '20250001',
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        $this->actingAs($user)
            ->from('/candidate/register')
            ->post('/candidate/register', [
                'idnumber' => '20250001',
                'name' => 'Test Student',
                'email' => 'student@example.test',
                'course_id' => 1,
                'position_id' => 1,
                'description' => 'Test platform description',
                'platforms' => 'Test platform',
                'profile_img' => UploadedFile::fake()->image('profile.jpg'),
                'banner_img' => UploadedFile::fake()->image('banner.jpg'),
            ])
            ->assertRedirect('/candidate/register')
            ->assertSessionHasErrors('idnumber');

        $this->assertDatabaseCount('candidate', 1);
        Storage::disk('public')->assertDirectoryEmpty('upload/profile-picture');
        Storage::disk('public')->assertDirectoryEmpty('upload/banner');
    }

    public function test_candidate_validation_error_hides_existing_voter_pending_indicator_until_errors_are_cleared(): void
    {
        $user = User::query()->create([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
            'idnumber' => '20250001',
        ]);
        $user->forceFill(['voter_verification_submitted_at' => now()])->save();

        $this->actingAs($user)
            ->from('/candidate/register')
            ->post('/candidate/register', [])
            ->assertRedirect('/candidate/register')
            ->assertSessionHasErrors(['idnumber', 'name', 'email', 'course_id', 'position_id']);

        $this->get('/candidate/register')
            ->assertOk()
            ->assertSee('id="candidate-error-toast"', false)
            ->assertDontSee('Pending Candidate Application');

        $this->assertNotNull($user->fresh()->voter_verification_submitted_at);

        $this->get('/candidate/register')
            ->assertOk()
            ->assertDontSee('Pending Candidate Application')
            ->assertSee('name="position_id"', false);
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
