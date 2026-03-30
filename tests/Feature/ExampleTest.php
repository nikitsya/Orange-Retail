<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_home_page_is_available_to_guests(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Login')
            ->assertSee('Register');
    }
}
