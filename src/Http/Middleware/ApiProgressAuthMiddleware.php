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

        // Handle login form submission
        if ($request->isMethod('post') && $request->has(['email', 'password'])) {
            $email = $request->input('email');
            $password = $request->input('password');

            $developer = ApiptDeveloper::where('email', $email)->first();

            if ($developer && Hash::check($password, $developer->password)) {
                session([
                    'apipt_user_id' => $developer->id,
                    'apipt_user_name' => $developer->name,
                    'apipt_user_email' => $developer->email,
                ]);

                return redirect()->route('apipt.dashboard');
            }

            return back()->withErrors(['credentials' => 'Invalid email or password']);
        }

        // Show login form if not authenticated
        if (!session('apipt_user_id')) {
            return response()->view('api-progress-tracker::auth.login');
        }

        return $next($request);
    }
}
