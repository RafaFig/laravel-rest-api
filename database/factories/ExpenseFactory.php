<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->text('191'),
            'amount' => $this->faker->randomFloat(2, 0, 5),
            'occurred_at' => $this->faker->date('Y-m-d')
        ];
    }
}
