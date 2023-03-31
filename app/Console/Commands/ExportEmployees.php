<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;

class ExportEmployees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:employees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export all employees to a JSON file.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $employees = Employee::all();
        $json = json_encode($employees, JSON_PRETTY_PRINT);
        $publicPath = public_path('employees.json');
        file_put_contents($publicPath, $json);
        $this->info('Exported employees to ' . $publicPath);
    }
}
