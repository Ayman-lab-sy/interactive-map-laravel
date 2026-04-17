<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class LogRateLimitedRequests
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response->getStatusCode() === Response::HTTP_TOO_MANY_REQUESTS) {
            DB::connection('cases')->table('case_audit_logs')->insert([
                'action' => 'rate_limited',
                'case_id' => null,
                'ip_hash' => hash('sha256', $request->ip()),
                'user_agent' => substr($request->userAgent(), 0, 255),
                'locale' => app()->getLocale(),
                'created_at' => now(),
            ]);
        }

        return $response;
    }
}
