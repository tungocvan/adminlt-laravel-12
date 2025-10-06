<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'php artisan clean:tokens';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \App\Models\TempToken::where('expires_at', '<', now())->delete();
    }
    
}
