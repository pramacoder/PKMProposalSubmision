<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Halaman root diarahkan ke halaman login.
     */
    public function test_the_root_redirects_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirectToRoute('login');
    }
}
