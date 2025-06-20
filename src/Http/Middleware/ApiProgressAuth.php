<?php

namespace Gmrakibulhasan\ApiProgressTracker\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Gmrakibulhasan\ApiProgressTracker\Models\ApiptDeveloper;

class ApiProgressAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is already authenticated for API Progress
        if ($request->session()->has('apipt_user_id')) {
            $userId = $request->session()->get('apipt_user_id');
            $user = ApiptDeveloper::find($userId);
            
            if ($user) {
                $request->attributes->set('apipt_user', $user);
                return $next($request);
            }
        }

        // If it's a login attempt
        if ($request->isMethod('POST') && $request->has(['email', 'password'])) {
            $user = ApiptDeveloper::where('email', $request->email)->first();
            
            if ($user && Hash::check($request->password, $user->password)) {
                $request->session()->put('apipt_user_id', $user->id);
                $request->session()->put('apipt_user_name', $user->name);
                $request->attributes->set('apipt_user', $user);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Login successful',
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email
                        ]
                    ]);
                }
                
                return redirect()->route('apipt.dashboard');
            }
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }
            
            return back()->withErrors(['email' => 'Invalid credentials']);
        }

        // If it's a logout request
        if ($request->isMethod('POST') && $request->has('logout')) {
            $request->session()->forget(['apipt_user_id', 'apipt_user_name']);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Logged out successfully']);
            }
            
            return redirect()->route('apipt.dashboard');
        }

        // Show login form
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }
        
        return response()->view('api-progress-tracker::auth.login');
    }
}
