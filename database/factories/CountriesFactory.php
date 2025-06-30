<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Countries;

class CountriesFactory extends Factory
{
    protected $model = Countries::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->country(),
        ];
    }
}
