<?php

namespace Gmrakibulhasan\ApiProgressTracker\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class ValidateInstallationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-progress:validate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate API Progress Tracker installation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Validating API Progress Tracker installation...');
        $this->newLine();

        $checks = [
            'Database Connection' => $this->checkDatabaseConnection(),
            'Tables Exist' => $this->checkTables(),
            'Models Work' => $this->checkModels(),
            'Configuration' => $this->checkConfiguration(),
            'Routes Available' => $this->checkRoutes(),
        ];

        $allPassed = true;

        foreach ($checks as $checkName => $result) {
            if ($result['status']) {
                $this->info("✅ {$checkName}: {$result['message']}");
            } else {
                $this->error("❌ {$checkName}: {$result['message']}");
                $allPassed = false;
            }
        }

        $this->newLine();

        if ($allPassed) {
            $this->info('🎉 All checks passed! Your API Progress Tracker is ready to use.');
            $this->info('📍 Access dashboard at: /api-progress');
            $this->info('🔑 Admin login: admin@apipt.com / password');
        } else {
            $this->error('❌ Some checks failed. Please review the errors above.');
            $this->info('💡 Try running: php artisan api-progress:migrate --fresh --seed');
        }

        return $allPassed ? 0 : 1;
    }

    private function checkDatabaseConnection()
    {
        try {
            DB::connection('apipt')->getPdo();
            return ['status' => true, 'message' => 'Connected successfully'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Connection failed: ' . $e->getMessage()];
        }
    }

    private function checkTables()
    {
        try {
            $tables = ['apipt_developers', 'apipt_api_progress', 'apipt_tasks', 'apipt_comments'];
            $missingTables = [];

            foreach ($tables as $table) {
                try {
                    DB::connection('apipt')->table($table)->first();
                } catch (\Exception $e) {
                    $missingTables[] = $table;
                }
            }

            if (empty($missingTables)) {
                return ['status' => true, 'message' => 'All tables exist'];
            } else {
                return ['status' => false, 'message' => 'Missing tables: ' . implode(', ', $missingTables)];
            }
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Error checking tables: ' . $e->getMessage()];
        }
    }

    private function checkModels()
    {
        try {
            $devCount = \Gmrakibulhasan\ApiProgressTracker\Models\ApiptDeveloper::count();
            $apiCount = \Gmrakibulhasan\ApiProgressTracker\Models\ApiptApiProgress::count();

            return [
                'status' => true,
                'message' => "Models working ({$devCount} developers, {$apiCount} API endpoints)"
            ];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Model error: ' . $e->getMessage()];
        }
    }

    private function checkConfiguration()
    {
        try {
            $config = config('api-progress-tracker.database');
            if ($config && isset($config['database'])) {
                return ['status' => true, 'message' => 'Configuration loaded'];
            } else {
                return ['status' => false, 'message' => 'Configuration missing or invalid'];
            }
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Configuration error: ' . $e->getMessage()];
        }
    }

    private function checkRoutes()
    {
        try {
            $routes = Route::getRoutes();
            $apiProgressRoutes = 0;

            foreach ($routes as $route) {
                if (str_contains($route->uri(), 'api-progress')) {
                    $apiProgressRoutes++;
                }
            }

            if ($apiProgressRoutes > 0) {
                return ['status' => true, 'message' => "{$apiProgressRoutes} routes registered"];
            } else {
                return ['status' => false, 'message' => 'No routes found'];
            }
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Route check error: ' . $e->getMessage()];
        }
    }
}
