<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Client;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'age' => $this->faker->numberBetween(18, 65),
            'address' => $this->faker->address(),
            'married' => $this->faker->boolean(),
            'country_id' => \App\Models\Countries::inRandomOrder()->first()->id ?? 1,
        ];
    }
}
