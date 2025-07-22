<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'username' => $this->faker->unique()->userName(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'is_admin' => false,
            'birthdate' => $this->faker->date('Y-m-d', '-18 years'),
            'remember_token' => Str::random(10),
        ];
    }

    public function configure(): static
    {
        return $this;
    }

    /**
     * State: Unverified email.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * State: Admin user with assigned role.
     */
    public function admin(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('admin');
            $user->update(['is_admin' => true]);
        });
    }

    /**
     * State: Regular user with assigned role.
     */
    public function regular(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('user');
        });
    }
}
