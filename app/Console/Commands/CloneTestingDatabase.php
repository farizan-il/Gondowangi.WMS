<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CloneTestingDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clone-test';

    protected $description = 'Clone production database to testing database';

    public function handle()
    {
        $sourceDb = config('database.connections.mysql.database');
        $targetDb = config('database.connections.mysql_testing.database');
        $user = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');

        $this->info("Cloning database from $sourceDb to $targetDb...");

        $dumpFile = storage_path('app/temp_production_dump.sql');
        $dumpFile = str_replace('\\', '/', $dumpFile);
        
        // Export
        $exportCmd = sprintf(
            'mysqldump -u %s %s %s --result-file=%s',
            $user,
            $password ? "-p$password" : "",
            escapeshellarg($sourceDb),
            escapeshellarg($dumpFile)
        );

        $this->info("Exporting production schema...");
        exec($exportCmd, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error("Failed to export production database.");
            return 1;
        }

        // Import
        $importCmd = sprintf(
            'mysql -u %s %s %s -e "source %s"',
            $user,
            $password ? "-p$password" : "",
            escapeshellarg($targetDb),
            $dumpFile
        );

        $this->info("Importing to testing database...");
        exec($importCmd, $output, $returnVar);

        if ($returnVar !== 0) {
            // Try fallback without -e source if it fails (using redirection via shell)
            $importCmdFallback = sprintf(
                'cmd /c "mysql -u %s %s %s < %s"',
                $user,
                $password ? "-p$password" : "",
                escapeshellarg($targetDb),
                escapeshellarg($dumpFile)
            );
            exec($importCmdFallback, $output, $returnVar);
        }

        if ($returnVar !== 0) {
            $this->error("Failed to import to testing database.");
            return 1;
        }

        // Clean up sessions in testing
        \DB::connection('mysql_testing')->table('sessions')->truncate();

        if (file_exists($dumpFile)) {
            unlink($dumpFile);
        }

        $this->info("Database cloned successfully!");
        return 0;
    }
}
