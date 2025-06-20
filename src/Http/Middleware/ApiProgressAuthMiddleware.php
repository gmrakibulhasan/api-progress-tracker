<?php

namespace Gmrakibulhasan\ApiProgressTracker\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Gmrakibulhasan\ApiProgressTracker\Models\ApiptDeveloper;

class ApiProgressAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is already authenticated
        if (session('apipt_user_id')) {
            return $next($request);
        }

        // Show login form if not authenticated
        return response()->view('api-progress-tracker::auth.login');
    }
}
