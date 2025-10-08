<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScoutRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scout:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh search index';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        ini_set('memory_limit', '2G');

        \Artisan::call("scout:import App\\\Models\\\Artist");
        \Artisan::call("scout:import App\\\Models\\\Album");
    }
}
