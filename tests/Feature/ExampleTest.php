<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_page_shows_today_beneficiary_data_without_login(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Data Penerima Manfaat')
            ->assertSee('Masuk Admin')
            ->assertDontSee('Masuk ke akun Anda');
    }
}
