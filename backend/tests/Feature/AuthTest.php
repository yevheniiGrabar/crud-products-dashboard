<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register()
    {
        // Arrange
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Act
        $response = $this->postJson('/api/auth/register', $userData);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email'],
                    'token',
                    'token_type'
                ]
            ])
            ->assertJson([
                'message' => 'User successfully registered',
                'data' => [
                    'user' => [
                        'name' => 'Test User',
                        'email' => 'test@example.com',
                    ]
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function user_can_login()
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        // Act
        $response = $this->postJson('/api/auth/login', $loginData);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email'],
                    'token',
                    'token_type'
                ]
            ])
            ->assertJson([
                'message' => 'Successful login',
                'data' => [
                    'user' => [
                        'email' => 'test@example.com',
                    ]
                ]
            ]);
    }

    /** @test */
    public function user_can_logout()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Act
        $response = $this->postJson('/api/auth/logout');

        // Assert
        $response->assertStatus(200)
            ->assertJson(['message' => 'Successful logout']);
    }

    /** @test */
    public function user_can_get_profile()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Act
        $response = $this->getJson('/api/auth/user');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'name', 'email']
                ]
            ])
            ->assertJson([
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ]
                ]
            ]);
    }

    /** @test */
    public function registration_validates_required_fields()
    {
        // Act
        $response = $this->postJson('/api/auth/register', []);

        // Assert
        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /** @test */
    public function registration_validates_email_format()
    {
        // Arrange
        $userData = [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Act
        $response = $this->postJson('/api/auth/register', $userData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function registration_validates_password_confirmation()
    {
        // Arrange
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
        ];

        // Act
        $response = $this->postJson('/api/auth/register', $userData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function registration_prevents_duplicate_email()
    {
        // Arrange
        User::factory()->create(['email' => 'test@example.com']);

        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Act
        $response = $this->postJson('/api/auth/register', $userData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function login_validates_credentials()
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $invalidData = [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ];

        // Act
        $response = $this->postJson('/api/auth/login', $invalidData);

        // Assert
        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials']);
    }

    /** @test */
    public function login_validates_required_fields()
    {
        // Act
        $response = $this->postJson('/api/auth/login', []);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /** @test */
    public function logout_requires_authentication()
    {
        // Act
        $response = $this->postJson('/api/auth/logout');

        // Assert
        $response->assertStatus(401);
    }

    /** @test */
    public function user_profile_requires_authentication()
    {
        // Act
        $response = $this->getJson('/api/auth/user');

        // Assert
        $response->assertStatus(401);
    }

    /** @test */
    public function password_must_be_minimum_length()
    {
        // Arrange
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123',
            'password_confirmation' => '123',
        ];

        // Act
        $response = $this->postJson('/api/auth/register', $userData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
}
