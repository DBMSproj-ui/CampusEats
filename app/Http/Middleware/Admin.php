<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Admin
{ 
    /**
     * Handle an incoming request.
     *
     * This middleware checks if the user is authenticated as an admin.
     * If not, it redirects them to the admin login page with an error message.
     *
     * @param  \Illuminate\Http\Request  $request   The current HTTP request instance.
     * @param  \Closure  $next                     The next middleware or request handler.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the admin is authenticated using the 'admin' guard
        if (!Auth::guard('admin')->check()) {
            // Redirect to admin login page with an error message if not authenticated
            return redirect()->route('admin.login')->with('error', 'You do not have permission to access this page');
        } 
         
        // If authenticated, allow the request to continue to the next step
        return $next($request);
    }
}
