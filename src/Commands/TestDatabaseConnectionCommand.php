<?php

namespace Gmrakibulhasan\ApiProgressTracker\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class TestDatabaseConnectionCommand extends Command
{
    protected $signature = 'api-progress:test-db';
    protected $description = 'Test the API Progress Tracker database connection';

    public function handle()
    {
        $this->info('Testing API Progress Tracker Database Connection...');

        // Display current configuration
        $config = config('api-progress-tracker.database');
        $this->line('');
        $this->line('Configuration:');
        $this->line('Connection: ' . $config['connection']);
        $this->line('Host: ' . $config['host']);
        $this->line('Port: ' . $config['port']);
        $this->line('Database: ' . $config['database']);
        $this->line('Username: ' . $config['username']);
        $this->line('Password: ' . (empty($config['password']) ? '(empty)' : '***'));

        // Test the apipt connection
        $this->line('');
        $this->line('Testing apipt connection...');
        try {
            $connection = DB::connection('apipt');
            $connection->getPdo();
            $this->info('✓ Database connection successful!');

            // Test if tables exist
            $this->line('');
            $this->line('Checking tables...');
            $tables = ['apipt_developers', 'apipt_api_progress', 'apipt_tasks', 'apipt_comments'];
            
            foreach ($tables as $table) {
                try {
                    $count = $connection->table($table)->count();
                    $this->info("✓ Table {$table} exists with {$count} records");
                } catch (\Exception $e) {
                    $this->error("✗ Table {$table} not found: " . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            $this->error('✗ Database connection failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
