<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Test health check endpoint.
     */
    public function test_health_check_endpoint(): void
    {
        $response = $this->get('/health');

        $response->assertStatus(200);
        $response->assertSee('healthy');
    }

    /**
     * Test database connection.
     */
    public function test_database_connection(): void
    {
        // Database bağlantısını test etmek için basit bir insert/select yapıyoruz
        $user = \App\Models\User::factory()->create();
        $this->assertDatabaseHas('users', [
            'id' => $user->id
        ]);
    }
}