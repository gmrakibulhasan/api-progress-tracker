<?php

namespace Gmrakibulhasan\ApiProgressTracker;

use Illuminate\Support\ServiceProvider;
use Gmrakibulhasan\ApiProgressTracker\Commands\SyncApiRoutesCommand;
use Livewire\Livewire;

class ApiProgressTrackerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/api-progress-tracker.php',
            'api-progress-tracker'
        );

        // Register separate database connection for the package
        $this->registerDatabaseConnection();
    }

    public function boot()
    {
        // Note: Migrations are NOT auto-loaded to prevent conflicts with main app migrations
        // Users must run: php artisan api-progress:migrate

        // Set default connection for package models
        $this->setModelConnections();

        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'api-progress-tracker');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncApiRoutesCommand::class,
                \Gmrakibulhasan\ApiProgressTracker\Commands\MigrateApiProgressCommand::class,
            ]);

            // Publish config
            $this->publishes([
                __DIR__ . '/../config/api-progress-tracker.php' => config_path('api-progress-tracker.php'),
            ], 'config');

            // Publish migrations
            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'migrations');

            // Publish views
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/api-progress-tracker'),
            ], 'views');

            // Publish assets
            $this->publishes([
                __DIR__ . '/../resources/assets' => public_path('vendor/api-progress-tracker'),
            ], 'assets');
        }

        // Register Livewire components
        $this->registerLivewireComponents();
    }

    private function registerLivewireComponents()
    {
        Livewire::component('apipt-dashboard', \Gmrakibulhasan\ApiProgressTracker\Livewire\Dashboard::class);
        // Other components will be registered when they are created
        // Livewire::component('apipt-developer-management', \Gmrakibulhasan\ApiProgressTracker\Livewire\DeveloperManagement::class);
        // Livewire::component('apipt-api-progress', \Gmrakibulhasan\ApiProgressTracker\Livewire\ApiProgressManagement::class);
        // Livewire::component('apipt-task-management', \Gmrakibulhasan\ApiProgressTracker\Livewire\TaskManagement::class);
        // Livewire::component('apipt-comment-system', \Gmrakibulhasan\ApiProgressTracker\Livewire\CommentSystem::class);
    }

    /**
     * Register separate database connection for the package
     */
    private function registerDatabaseConnection()
    {
        $config = config('api-progress-tracker.database');

        config(['database.connections.apipt' => [
            'driver' => $config['connection'],
            'host' => $config['host'],
            'port' => $config['port'],
            'database' => $config['database'],
            'username' => $config['username'],
            'password' => $config['password'],
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]]);
    }

    /**
     * Set the database connection for package models
     */
    private function setModelConnections()
    {
        // Set connection for all package models
        \Gmrakibulhasan\ApiProgressTracker\Models\ApiptDeveloper::setConnectionName('apipt');
        \Gmrakibulhasan\ApiProgressTracker\Models\ApiptApiProgress::setConnectionName('apipt');
        \Gmrakibulhasan\ApiProgressTracker\Models\ApiptTask::setConnectionName('apipt');
        \Gmrakibulhasan\ApiProgressTracker\Models\ApiptComment::setConnectionName('apipt');
    }
}
