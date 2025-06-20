<?php

namespace Gmrakibulhasan\ApiProgressTracker\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
     */    public function handle()
    {
        $this->info('Running API Progress Tracker migrations...');

        // Check database connection first
        try {
            $connection = DB::connection('apipt');
            $connection->getPdo();
            $this->info('Database connection successful.');
        } catch (\Exception $e) {
            $this->error('Cannot connect to API Progress Tracker database.');
            $this->error('Please ensure the database exists and connection settings are correct.');
            $this->error('Connection error: ' . $e->getMessage());
            return 1;
        }

        // Get the package migration files path relative to the project root
        $migrationPath = 'vendor/gmrakibulhasan/api-progress-tracker/database/migrations';

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
                $this->runSeeder();
                $this->info('Database seeded successfully!');
            }
        } catch (\Exception $e) {
            $this->error('Migration failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Run the package seeder manually
     */
    private function runSeeder()
    {
        try {
            // Import required models
            $developerClass = \Gmrakibulhasan\ApiProgressTracker\Models\ApiptDeveloper::class;
            $apiProgressClass = \Gmrakibulhasan\ApiProgressTracker\Models\ApiptApiProgress::class;
            $taskClass = \Gmrakibulhasan\ApiProgressTracker\Models\ApiptTask::class;

            // Create sample developers
            $admin = $developerClass::firstOrCreate(
                ['email' => 'admin@apipt.com'],
                [
                    'name' => 'Admin User',
                    'password' => Hash::make('password')
                ]
            );

            $developer1 = $developerClass::firstOrCreate(
                ['email' => 'john@example.com'],
                [
                    'name' => 'John Doe',
                    'password' => Hash::make('password')
                ]
            );

            $developer2 = $developerClass::firstOrCreate(
                ['email' => 'jane@example.com'],
                [
                    'name' => 'Jane Smith',
                    'password' => Hash::make('password')
                ]
            );

            // Create sample API progress entries
            $sampleApis = [
                [
                    'method' => 'GET',
                    'endpoint' => '/api/users',
                    'group_name' => 'User Management',
                    'description' => 'Get all users',
                    'priority' => 'high',
                    'status' => 'complete'
                ],
                [
                    'method' => 'POST',
                    'endpoint' => '/api/users',
                    'group_name' => 'User Management',
                    'description' => 'Create new user',
                    'priority' => 'high',
                    'status' => 'in_progress'
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/products',
                    'group_name' => 'Product Management',
                    'description' => 'Get all products',
                    'priority' => 'medium',
                    'status' => 'todo'
                ]
            ];

            foreach ($sampleApis as $apiData) {
                $apiProgressClass::firstOrCreate(
                    ['method' => $apiData['method'], 'endpoint' => $apiData['endpoint']],
                    $apiData
                );
            }

            // Create sample tasks
            $sampleTasks = [
                [
                    'title' => 'Implement user authentication',
                    'description' => 'Add JWT authentication to the API',
                    'priority' => 'high',
                    'status' => 'in_progress',
                    'assigned_by' => $admin->id
                ],
                [
                    'title' => 'Add input validation',
                    'description' => 'Implement proper validation for all endpoints',
                    'priority' => 'medium',
                    'status' => 'todo',
                    'assigned_by' => $admin->id
                ]
            ];

            foreach ($sampleTasks as $taskData) {
                $task = $taskClass::firstOrCreate(
                    ['title' => $taskData['title']],
                    $taskData
                );

                // Assign developers to tasks
                $task->developers()->syncWithoutDetaching([$developer1->id, $developer2->id]);
            }

            $this->info('✓ Created admin user (admin@apipt.com / password)');
            $this->info('✓ Created sample developers and API progress entries');
            $this->info('✓ Created sample tasks with assignments');
        } catch (\Exception $e) {
            $this->error('Seeding failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
