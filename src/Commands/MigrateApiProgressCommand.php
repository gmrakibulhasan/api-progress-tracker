<?php

namespace Gmrakibulhasan\ApiProgressTracker\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateApiProgressCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-progress:migrate {--fresh : Drop all tables and re-run all migrations} {--seed : Seed the database after migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations for API Progress Tracker on separate database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Running API Progress Tracker migrations...');

        // Get the package migration files
        $migrationPath = __DIR__ . '/../../database/migrations';

        try {
            if ($this->option('fresh')) {
                $this->info('Dropping all tables and re-running migrations...');

                // Run migrate:fresh with specific database and path
                Artisan::call('migrate:fresh', [
                    '--database' => 'apipt',
                    '--path' => $migrationPath,
                    '--force' => true,
                ]);
            } else {
                // Run normal migration with specific database and path
                Artisan::call('migrate', [
                    '--database' => 'apipt',
                    '--path' => $migrationPath,
                    '--force' => true,
                ]);
            }

            $this->info('Migrations completed successfully!');

            // Run seeder if requested
            if ($this->option('seed')) {
                $this->info('Seeding database...');
                Artisan::call('db:seed', [
                    '--database' => 'apipt',
                    '--class' => 'Gmrakibulhasan\\ApiProgressTracker\\Database\\Seeders\\ApiProgressTrackerSeeder',
                    '--force' => true,
                ]);
                $this->info('Database seeded successfully!');
            }
        } catch (\Exception $e) {
            $this->error('Migration failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
