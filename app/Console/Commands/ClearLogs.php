<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all log files.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $files = Storage::disk('logs')->files();
        $files = array_diff($files, ['.gitignore']);
        Storage::disk('logs')->delete($files);
        $this->info(sprintf('Deleted %d log files.', count($files)));
    }
}
