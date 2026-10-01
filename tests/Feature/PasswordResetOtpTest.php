<?php

namespace Tests\Feature;

use App\Mail\PasswordResetOtpMail;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class PasswordResetOtpTest extends TestCase
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
            $table->string('idnumber')->nullable()->unique();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->string('login_qr_hash')->nullable();
            $table->string('password_recovery_qr_hash')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('password_reset_otps', function (Blueprint $table): void {
            $table->id();
            $table->string('email')->unique();
            $table->string('code_hash')->nullable();
            $table->string('reset_token_hash')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function test_user_can_reset_password_with_email_otp(): void
    {
        Mail::fake();
        $user = User::query()->create([
            'name' => 'Test Voter',
            'email' => 'voter@example.test',
            'password' => 'old-password',
        ]);

        $this->post('/forgot-password', ['email' => $user->email])
            ->assertRedirect('/forgot-password/verify');

        $otp = null;
        Mail::assertSent(PasswordResetOtpMail::class, function (PasswordResetOtpMail $mail) use (&$otp): bool {
            $otp = $mail->otp;

            return true;
        });

        $this->post('/forgot-password/verify', ['otp' => $otp])
            ->assertRedirect('/reset-password');

        $this->post('/reset-password', [
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertRedirect('/login');

        $this->assertTrue(Hash::check('new-secure-password', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_otps', ['email' => $user->email]);
    }

    public function test_mail_transport_failure_is_reported_and_does_not_consume_resend_limit(): void
    {
        $user = User::query()->create([
            'name' => 'Test Voter',
            'email' => 'voter@example.test',
            'password' => 'old-password',
        ]);
        $limiterKey = 'password-otp-email:'.hash('sha256', $user->email);

        Mail::shouldReceive('to')
            ->once()
            ->with($user->email)
            ->andThrow(new TransportException('SMTP authentication failed.'));

        $this->post('/forgot-password', ['email' => $user->email])
            ->assertRedirect('/forgot-password')
            ->assertSessionHas('mail_error')
            ->assertSessionMissing('password_otp_email');

        $this->assertFalse(RateLimiter::tooManyAttempts($limiterKey, 1));
        $this->assertDatabaseMissing('password_reset_otps', ['email' => $user->email]);
    }

    public function test_recovery_qr_grants_a_single_password_reset(): void
    {
        $user = User::query()->create([
            'name' => 'QR Voter',
            'email' => 'qr-voter@example.test',
            'password' => 'old-password',
        ]);
        $secret = Str::random(64);
        $user->forceFill(['password_recovery_qr_hash' => Hash::make($secret)])->save();
        $qr = json_encode([
            'purpose' => 'password-recovery',
            'user_id' => $user->getKey(),
            'secret' => $secret,
        ]);

        $this->postJson('/forgot-password/qr', ['qr' => $qr])
            ->assertOk()
            ->assertJsonPath('ok', true);

        $this->assertNull($user->fresh()->password_recovery_qr_hash);
        $this->postJson('/forgot-password/qr', ['qr' => $qr])->assertUnprocessable();

        $this->post('/reset-password', [
            'password' => 'new-qr-password',
            'password_confirmation' => 'new-qr-password',
        ])->assertRedirect('/login');

        $this->assertTrue(Hash::check('new-qr-password', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_otps', ['email' => $user->email]);
    }

    public function test_login_qr_authenticates_only_with_the_account_secret(): void
    {
        $user = User::query()->create([
            'name' => 'Login QR Voter',
            'email' => 'login-qr-voter@example.test',
            'password' => 'old-password',
        ]);
        $secret = Str::random(64);
        $user->forceFill(['login_qr_hash' => Hash::make($secret)])->save();
        $payload = [
            'purpose' => 'login',
            'user_id' => $user->getKey(),
            'secret' => $secret,
        ];

        $this->postJson('/login/qr', ['qr' => json_encode($payload)])
            ->assertOk()
            ->assertJsonPath('ok', true);
        $this->assertAuthenticatedAs($user);

        auth()->logout();
        $payload['secret'] = Str::random(64);
        $this->postJson('/login/qr', ['qr' => json_encode($payload)])
            ->assertUnprocessable()
            ->assertJsonPath('ok', false);
    }
}