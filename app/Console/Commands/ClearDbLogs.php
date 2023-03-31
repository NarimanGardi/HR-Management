<?php

namespace App\Console\Commands;

use App\Models\EmployeeLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ClearDbLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:db-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete logs older than 1 month from the employee logs table.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $date = Carbon::now()->subMonth();
        EmployeeLog::where('created_at', '<', $date)->delete();
        $this->info('Old logs have been deleted from the database.');
    }
}
