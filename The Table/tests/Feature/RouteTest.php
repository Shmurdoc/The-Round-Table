<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test public routes are accessible
     */
    public function test_home_page_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_about_page_is_accessible(): void
    {
        $response = $this->get('/about');
        $response->assertStatus(200);
    }

    public function test_how_it_works_page_is_accessible(): void
    {
        $response = $this->get('/how-it-works');
        $response->assertStatus(200);
    }

    public function test_cohorts_index_requires_authentication(): void
    {
        $response = $this->get('/cohorts');
        $response->assertRedirect('/login');
    }

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_register_page_is_accessible(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    /**
     * Test protected routes redirect to login
     */
    public function test_dashboard_requires_authentication(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_profile_requires_authentication(): void
    {
        $response = $this->get('/profile');
        $response->assertRedirect('/login');
    }

    public function test_kyc_requires_authentication(): void
    {
        $response = $this->get('/kyc');
        $response->assertRedirect('/login');
    }
}
