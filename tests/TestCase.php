<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // use CreatesApplication;
    protected function assertApiValidationError($response, array $fields = [])
    {
        $response
            ->assertStatus(422)
            ->assertJsonPath('error', true)
            ->assertJsonPath('message', 'Validation failed.');

        foreach ($fields as $field) {
            $response->assertJsonStructure([
                'data' => [
                    'errors' => [
                        $field
                    ]
                ]
            ]);
        }
    }

    /**
     * Assert that the API response has the expected structure and content.
     *
     * @param mixed $response The response object to assert against.
     * @param string $message The expected message in the response.
     * @param int $status The expected HTTP status code of the response.
     * @param bool $error Indicates whether the response should indicate an error (default: false).
     */
    protected function assertApi(mixed $response, string $message, int $status, bool $error = false)
    {
        $response
            ->assertStatus($status)
            ->assertJsonPath('error', $error)
            ->assertJsonPath('message', $message);
    }

    /**
     * Helper method to log in as an admin user and retrieve the authentication token.
     *
     * This method creates a new admin user using a factory, generates an authentication
     * token for that user, and returns the token as a string. This token can be used
     * in subsequent API requests to authenticate as the admin user.
     *
     * @return string The authentication token for the admin user.
     */
    protected function loginAdmin(): string
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $token = $admin->createToken('test-token')->plainTextToken;
        return $token;
    }
}
