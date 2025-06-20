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
            __DIR__.'/../config/api-progress-tracker.php',
            'api-progress-tracker'
        );
    }

    public function boot()
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'api-progress-tracker');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncApiRoutesCommand::class,
            ]);

            // Publish config
            $this->publishes([
                __DIR__.'/../config/api-progress-tracker.php' => config_path('api-progress-tracker.php'),
            ], 'config');

            // Publish migrations
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'migrations');

            // Publish views
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/api-progress-tracker'),
            ], 'views');

            // Publish assets
            $this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/api-progress-tracker'),
            ], 'assets');
        }

        // Register Livewire components
        $this->registerLivewireComponents();
    }

    private function registerLivewireComponents()
    {
        Livewire::component('apipt-dashboard', \Gmrakibulhasan\ApiProgressTracker\Livewire\Dashboard::class);
        Livewire::component('apipt-developer-management', \Gmrakibulhasan\ApiProgressTracker\Livewire\DeveloperManagement::class);
        Livewire::component('apipt-api-progress', \Gmrakibulhasan\ApiProgressTracker\Livewire\ApiProgressManagement::class);
        Livewire::component('apipt-task-management', \Gmrakibulhasan\ApiProgressTracker\Livewire\TaskManagement::class);
        Livewire::component('apipt-comment-system', \Gmrakibulhasan\ApiProgressTracker\Livewire\CommentSystem::class);
    }
}
