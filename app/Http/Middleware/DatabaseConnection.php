<?php

namespace App\Http\Middleware;

use Closure;
use Config;
use DB;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DatabaseConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $defaultConnection = 'es_db';
        $connection = session('db_connection', $defaultConnection);
        Config::set('database.default', $connection);

        return $next($request);
    }
}
