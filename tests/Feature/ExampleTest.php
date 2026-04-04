<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_home_page_is_available_to_guests(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Browse supermarket products')
            ->assertSee('Login')
            ->assertSee('Register');
    }
}
