<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request)
            ->header('Access-Control-Allow-Origin', 'http://localhost:8080')
            ->header('Access-Control-Allow-Methods', '*')
            ->header('Access-Control-Allow-Headers', 'Content-Type,Access-Control-Expose-Headers,Authorization,X-Requested-With');
    }
}
