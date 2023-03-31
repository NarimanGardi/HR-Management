<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ExportDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:database {filename?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export the database to a SQL file.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $filename = $this->argument('filename') ?: 'database.sql';
        // make public path
        $path = Storage::disk('local')->path($filename);
        $command = sprintf('mysqldump -u%s -p%s %s > %s', env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_DATABASE'), $path);
        exec($command);
        $this->info(sprintf('Database exported to %s', $path));
    }
}
