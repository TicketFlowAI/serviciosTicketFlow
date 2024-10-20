<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use WithFaker;

    private $email = "dennis.ocana@mindsoft.biz";
    private $password = "EstaEsUnaCotraseña123*";
    public function testUserLogin()
    {
        $response = $this->postJson('/login', [
            'email' => $this->email,
            'password' => $this->password
        ]);
        $response->assertStatus(200);
        $this->assertAuthenticated();
    }
}
