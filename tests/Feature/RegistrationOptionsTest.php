<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class RegistrationOptionsTest extends TestCase
{
    public function test_login_create_account_link_goes_to_account_creation_page(): void
    {
        config(['app.url' => 'http://localhost']);
        URL::forceRootUrl('http://localhost');

        $this->get('/login')
            ->assertOk()
            ->assertSee('Create Account')
            ->assertSee('href="' . route('register') . '"', false)
            ->assertSee('fixed inset-0 z-0 overflow-hidden', false)
            ->assertSee('images/background1.jpg', false)
            ->assertSee('object-cover object-bottom', false);
    }

    public function test_registration_options_link_to_candidate_and_voter_flows(): void
    {
        config(['app.url' => 'http://localhost']);
        URL::forceRootUrl('http://localhost');

        $this->get('/register/options')
            ->assertOk()
            ->assertSee('Candidate Registration')
            ->assertSee(route('candidate.register'), false)
            ->assertSee('Voter Registration')
            ->assertSee(route('login'), false)
            ->assertSee('Log In to Verify');

        $this->get('/register')
            ->assertOk()
            ->assertSee('Create Account')
            ->assertSee(route('register.store'), false)
            ->assertSee('class="relative min-h-screen bg-blue-900 text-center text-white"', false)
            ->assertSee('fixed inset-0 z-0 overflow-hidden', false)
            ->assertSee('images/background1.jpg', false)
            ->assertSee('object-cover object-bottom', false)
            ->assertDontSee('Choose the type of registration you need.');

        $this->get('/register?idnumber=20250010&email=qr%40example.test&name=Jamie+R+Student')
            ->assertOk()
            ->assertSee('value="20250010"', false)
            ->assertSee('value="qr@example.test"', false)
            ->assertSee('value="Jamie"', false)
            ->assertSee('value="R."', false)
            ->assertSee('value="Student"', false);
    }
}
