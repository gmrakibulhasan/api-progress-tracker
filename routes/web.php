<?php

use Illuminate\Support\Facades\Route;
use Gmrakibulhasan\ApiProgressTracker\Http\Controllers\ApiProgressController;

Route::prefix(config('api-progress-tracker.route.prefix', 'api-progress'))
    ->middleware(config('api-progress-tracker.route.middleware', ['web']))
    ->name(config('api-progress-tracker.route.name', 'apipt.'))
    ->group(function () {

        // Authentication routes (without auth middleware)
        Route::post('/', [ApiProgressController::class, 'login'])->name('login');

        // Protected routes (with auth middleware)
        Route::middleware(['apipt.auth'])->group(function () {
            // Main dashboard route
            Route::get('/', [ApiProgressController::class, 'index'])->name('dashboard');

            // Authentication routes
            Route::post('/logout', [ApiProgressController::class, 'logout'])->name('logout');

            // API endpoints for AJAX requests
            Route::prefix('api')->group(function () {

                // Sync routes
                Route::post('sync-routes', [ApiProgressController::class, 'syncRoutes'])->name('api.sync-routes');

                // Dashboard stats
                Route::get('stats', [ApiProgressController::class, 'getDashboardStats'])->name('api.stats');

                // Developers
                Route::get('developers', [ApiProgressController::class, 'getDevelopers'])->name('api.developers');
                Route::post('developers', [ApiProgressController::class, 'storeDeveloper'])->name('api.developers.store');
                Route::put('developers/{id}', [ApiProgressController::class, 'updateDeveloper'])->name('api.developers.update');
                Route::delete('developers/{id}', [ApiProgressController::class, 'deleteDeveloper'])->name('api.developers.delete');

                // API Progress
                Route::get('api-progress', [ApiProgressController::class, 'getApiProgress'])->name('api.progress');
                Route::post('api-progress', [ApiProgressController::class, 'storeApiProgress'])->name('api.progress.store');
                Route::put('api-progress/{id}', [ApiProgressController::class, 'updateApiProgress'])->name('api.progress.update');
                Route::delete('api-progress/{id}', [ApiProgressController::class, 'deleteApiProgress'])->name('api.progress.delete');

                // Tasks
                Route::get('tasks', [ApiProgressController::class, 'getTasks'])->name('api.tasks');
                Route::post('tasks', [ApiProgressController::class, 'storeTask'])->name('api.tasks.store');
                Route::put('tasks/{id}', [ApiProgressController::class, 'updateTask'])->name('api.tasks.update');
                Route::delete('tasks/{id}', [ApiProgressController::class, 'deleteTask'])->name('api.tasks.delete');

                // Comments
                Route::get('comments/{type}/{id}', [ApiProgressController::class, 'getComments'])->name('api.comments');
                Route::post('comments', [ApiProgressController::class, 'storeComment'])->name('api.comments.store');
                Route::put('comments/{id}', [ApiProgressController::class, 'updateComment'])->name('api.comments.update');
                Route::delete('comments/{id}', [ApiProgressController::class, 'deleteComment'])->name('api.comments.delete');

                // File uploads
                Route::post('upload', [ApiProgressController::class, 'uploadFile'])->name('api.upload');

                // Statistics
                Route::get('stats', [ApiProgressController::class, 'getStatistics'])->name('api.stats');
            });
        }); // End protected routes
    }); // End main route group
