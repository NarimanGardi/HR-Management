<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;

class EmployeeFake extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fake:employee {count?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert 1000 fake employees into the employees table';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $count = $this->argument('count') ?: 1000;
        Employee::withoutEvents(function () use ($count) {

        $bar = $this->output->createProgressBar($count);
        $bar->start();
        for ($i = 0; $i < $count; $i++) {
            Employee::factory()->create();
            $bar->advance();
        }
        $bar->finish();
        });
        $this->info("\nInserted $count fake employees into the employees table.");
    }
}
