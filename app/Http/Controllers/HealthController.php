<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = [
            'status' => 'ok',
            'database' => 'disconnected',
            'cache' => 'disconnected',
        ];

        try {
            DB::connection()->getPdo();
            $checks['database'] = 'connected';
        } catch (\Exception) {
            $checks['database'] = 'disconnected';
            $checks['status'] = 'degraded';
        }

        try {
            cache()->store()->get('health-check-test');
            $checks['cache'] = 'connected';
        } catch (\Exception) {
            $checks['cache'] = 'disconnected';
        }

        $statusCode = $checks['status'] === 'ok' ? 200 : 503;

        return response()->json($checks, $statusCode);
    }
}
