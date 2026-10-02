<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LoginErrorFeedbackTest extends TestCase
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
            $table->string('email')->unique();
            $table->string('password');
        });
    }

    public function test_failed_login_shows_timed_toast_and_retains_username_without_password(): void
    {
        $this->from('/login')->post('/login', [
            'email' => 'student@example.test',
            'password' => 'incorrect-password',
        ])->assertRedirect('/login')
            ->assertSessionHas('_old_input.email', 'student@example.test');

        $this->assertArrayNotHasKey('password', session()->get('_old_input', []));

        $response = $this->get('/login');
        $response->assertOk()
            ->assertSee('id="login-error-toast"', false)
            ->assertSee('popover="manual"', false)
            ->assertSee('setTimeout(dismiss, 5000)', false)
            ->assertSee('Invalid credentials')
            ->assertSee('id="username" name="email" type="text" placeholder="Enter your username" value="student@example.test"', false);
    }
}
