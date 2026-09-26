<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_stylesheet_uses_same_origin_and_routes_use_forwarded_host_and_https(): void
    {
        $this->withServerVariables([
            'HTTP_X_FORWARDED_HOST' => 'tasks.test',
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ])->get('/')
            ->assertSee('href="/css/app.css?v=', false)
            ->assertSee('href="https://tasks.test/tasks/create"', false);
    }
}
