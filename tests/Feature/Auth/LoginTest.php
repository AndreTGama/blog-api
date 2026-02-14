<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test login.
     */
    public function test_login_successfully(): void
    {
        $email = 'user_teste@email.com';
        $password = 'password';

        User::factory()->create([
            'email' => $email,
            'password' => $password,
        ]);

        $response = $this->postJson(route('auth.login'), [
            'email' => $email,
            'password' => $password,
        ]);

        $this->assertApi($response, 'Login successful.', 200);
    }

    /**
     * Test login with invalid credentials.
     */
    public function test_login_with_invalid_credentials(): void
    {
        $response = $this->postJson(route('auth.login'), [
            'email' => 'invalid_email',
            'password' => 'invalid_password',
        ]);

        $this->assertApiValidationError($response, ['email']);
    }

    /**
     * Test login with missing fields.
     */
    public function test_login_with_missing_fields(): void
    {
        $response = $this->postJson(route('auth.login'), []);

        $this->assertApiValidationError($response, ['email', 'password']);
    }

    /**
     * Test login with incorrect password.
     */
    public function test_login_with_incorrect_password(): void
    {
        $email = 'user_teste@email.com';
        $password = 'password';

        User::factory()->create([
            'email' => $email,
            'password' => $password,
        ]);

        $response = $this->postJson(route('auth.login'), [
            'email' => $email,
            'password' => 'wrong_password',
        ]);

        $this->assertApi($response, 'Login failed.', 401, true);
    }

    /**
     * Test login with non-existent email.
     */
    public function test_login_with_non_existent_email(): void
    {
        $response = $this->postJson(route('auth.login'), [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $this->assertApi($response, 'Login failed.', 401, true);
    }
}
