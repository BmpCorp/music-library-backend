<?php

namespace App\Console\Commands;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Console\Command;

class DbMock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:mock {--ai}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed database with mock data';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        set_time_limit(0);

        $withAi = $this->hasOption('ai');
        (new DatabaseSeeder())->mock($withAi);
    }
}
