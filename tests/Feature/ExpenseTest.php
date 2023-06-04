<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_authenticated_can_get_all_expenses()
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->getJson(
            "/api/users/{$user->id}/expenses",
            ['Authorization' => "Bearer {$token}"]
        );

        $response->assertStatus(200);
    }

    public function test_user_authenticated_can_create_new_expense()
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->postJson("/api/users/{$user->id}/expenses", [
            'description' => 'Pagamento da fatura do cartão de crédito',
            'amount' => 1500.15,
            'occurred_at' => '2023-06-03',
        ], ['Authorization' => "Bearer {$token}"]);

        $response->assertStatus(201);
    }

    public function test_user_authenticated_can_update_expense()
    {
        $user = User::factory()->create();
        $expense = Expense::factory()->create(['user_id' => $user->id]);

        $token = auth('api')->login($user);

        $response = $this->putJson("/api/users/{$user->id}/expenses/{$expense->id}", [
            'description' => 'Pagamento da fatura do cartão de crédito',
            'amount' => 1500.15,
            'occurred_at' => '2023-06-03',
        ], ['Authorization' => "Bearer {$token}"]);

        $response->assertStatus(200);
    }

    public function test_user_authenticated_can_get_expense()
    {
        $user = User::factory()->create();
        $expense = Expense::factory()->create(['user_id' => $user->id]);

        $token = auth('api')->login($user);

        $response = $this->get(
            "/api/users/{$user->id}/expenses/{$expense->id}",
            ['Authorization' => "Bearer {$token}"]
        );

        $response->assertStatus(200);
    }

    public function test_user_authenticated_can_delete_expense()
    {
        $user = User::factory()->create();
        $expense = Expense::factory()->create(['user_id' => $user->id]);

        $token = auth('api')->login($user);

        $response = $this->delete(
            "/api/users/{$user->id}/expenses/{$expense->id}",
            [],
            ['Authorization' => "Bearer {$token}"]
        );

        $response->assertStatus(200);
    }
}
