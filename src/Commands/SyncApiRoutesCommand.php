<?php

namespace Gmrakibulhasan\ApiProgressTracker\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Gmrakibulhasan\ApiProgressTracker\Models\ApiptApiProgress;

class SyncApiRoutesCommand extends Command
{
    protected $signature = 'api-progress:sync-routes 
                            {--force : Force sync even if routes already exist}
                            {--group= : Only sync routes from specific group}';

    protected $description = 'Sync API routes with API Progress Tracker';

    protected $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];

    public function handle()
    {
        $this->info('Starting API routes synchronization...');

        $routes = $this->getApiRoutes();
        $syncedCount = 0;
        $skippedCount = 0;

        foreach ($routes as $route) {
            $routeData = $this->extractRouteData($route);

            if ($this->shouldSyncRoute($routeData)) {
                $existing = ApiptApiProgress::where('method', $routeData['method'])
                    ->where('endpoint', $routeData['endpoint'])
                    ->first();

                if (!$existing || $this->option('force')) {
                    if ($existing && $this->option('force')) {
                        $existing->update($routeData);
                        $this->line("Updated: {$routeData['method']} {$routeData['endpoint']}");
                    } else {
                        ApiptApiProgress::create($routeData);
                        $this->line("Added: {$routeData['method']} {$routeData['endpoint']}");
                    }
                    $syncedCount++;
                } else {
                    $this->comment("Skipped (exists): {$routeData['method']} {$routeData['endpoint']}");
                    $skippedCount++;
                }
            }
        }

        $this->info("Synchronization completed!");
        $this->info("Routes synced: {$syncedCount}");
        $this->info("Routes skipped: {$skippedCount}");

        return Command::SUCCESS;
    }

    protected function getApiRoutes(): array
    {
        $routes = collect(RouteFacade::getRoutes())->filter(function (Route $route) {
            $uri = $route->uri();
            $methods = $route->methods();

            // Filter API routes (starting with 'api/' or containing 'api')
            $isApiRoute = str_starts_with($uri, 'api/') ||
                str_contains($uri, '/api/') ||
                str_contains($route->getName() ?? '', 'api.');

            // Check if route has valid HTTP methods
            $hasValidMethod = !empty(array_intersect($methods, $this->methods));

            // Exclude our own package routes
            $isNotPackageRoute = !str_contains($uri, 'api-progress');

            return $isApiRoute && $hasValidMethod && $isNotPackageRoute;
        });

        // Filter by group if specified
        if ($group = $this->option('group')) {
            $routes = $routes->filter(function (Route $route) use ($group) {
                return $this->getRouteGroup($route) === $group;
            });
        }

        return $routes->toArray();
    }

    protected function extractRouteData(Route $route): array
    {
        $methods = array_intersect($route->methods(), $this->methods);
        $method = reset($methods) ?: 'GET';

        return [
            'method' => $method,
            'endpoint' => '/' . ltrim($route->uri(), '/'),
            'group_name' => $this->getRouteGroup($route),
            'description' => $this->getRouteDescription($route),
            'priority' => 'normal',
            'status' => 'todo',
        ];
    }

    protected function getRouteGroup(Route $route): ?string
    {
        $action = $route->getAction();

        // Try to get group from middleware
        if (isset($action['middleware'])) {
            $middleware = is_array($action['middleware']) ? $action['middleware'] : [$action['middleware']];
            foreach ($middleware as $m) {
                if (str_contains($m, 'api:')) {
                    return str_replace('api:', '', $m);
                }
            }
        }

        // Try to get from controller namespace
        if (isset($action['controller'])) {
            $controller = $action['controller'];
            if (str_contains($controller, '\\')) {
                $parts = explode('\\', $controller);
                // Look for API-related namespace parts
                foreach ($parts as $part) {
                    if (str_contains(strtolower($part), 'api') && $part !== 'Api') {
                        return $part;
                    }
                }
            }
        }

        // Extract from URI structure
        $uri = $route->uri();
        $segments = explode('/', trim($uri, '/'));

        if (count($segments) >= 2 && $segments[0] === 'api') {
            return ucfirst($segments[1]);
        }

        return 'General';
    }

    protected function getRouteDescription(Route $route): ?string
    {
        $action = $route->getAction();

        // Try to get from route name
        if ($name = $route->getName()) {
            return ucwords(str_replace(['.', '_', '-'], ' ', $name));
        }

        // Generate from controller and method
        if (isset($action['controller'])) {
            $controller = $action['controller'];
            if (str_contains($controller, '@')) {
                [$class, $method] = explode('@', $controller);
                $className = class_basename($class);
                return "{$className}::{$method}";
            }
        }

        // Generate from URI
        return 'API Endpoint: ' . $route->uri();
    }

    protected function shouldSyncRoute(array $routeData): bool
    {
        // Skip if endpoint is too generic or contains parameters only
        $endpoint = $routeData['endpoint'];

        if ($endpoint === '/' || $endpoint === '/api' || $endpoint === '/api/') {
            return false;
        }

        // Skip if endpoint is just parameters
        if (preg_match('/^\/(\{[^}]+\}\/)*\{[^}]+\}$/', $endpoint)) {
            return false;
        }

        return true;
    }
}
