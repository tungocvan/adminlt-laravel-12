<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\UserSeeder;

class SeedUsers extends Command
{
    protected $signature = 'db:seed:users {count=10}';
    protected $description = 'Seed a specific number of regular users';

    public function handle(): int
    {
        $count = (int) $this->argument('count');

        $this->info("Seeding {$count} users...");

        // Gọi seeder và truyền tham số
        (new UserSeeder)->run($count);

        $this->info("✅ Done seeding {$count} users.");

        return Command::SUCCESS;
    }
}
