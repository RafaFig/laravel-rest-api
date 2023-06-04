<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
  use RefreshDatabase;

  public function test_user_sign_up()
  {
    $response = $this->postJson('/api/users', [
      'name' => 'Fulano de Tal',
      'email' => 'tal@fulano.com.br',
      'password' => '12345678'
    ]);

    $response->assertStatus(201);
  }

  public function test_user_authentication()
  {
    $user = User::factory()->create();

    $response = $this->postJson('/api/auth', [
      'email' => $user->email,
      'password' => '12345678'
    ]);

    $response->assertStatus(200)
      ->assertJsonFragment(['expires_in' => 3600]);
  }
}
