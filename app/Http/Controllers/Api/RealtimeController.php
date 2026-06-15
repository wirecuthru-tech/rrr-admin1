<?php

namespace App\Http\Controllers\Api;

use App\Events\AppRealtimeEvent;
use App\Events\PrivateRealtimeEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RealtimeController extends Controller
{
    public function config(Request $request)
    {
        return response()->json([
            'success' => true,
            'realtime' => [
                'enabled' => (bool) env('RRR_REALTIME_ENABLED', true),
                'driver' => env('BROADCAST_CONNECTION', 'reverb'),
                'app_key' => env('REVERB_APP_KEY'),
                'host' => env('REVERB_PUBLIC_HOST', env('REVERB_HOST')),
                'port' => (int) env('REVERB_PUBLIC_PORT', env('REVERB_PORT', 443)),
                'scheme' => env('REVERB_PUBLIC_SCHEME', env('REVERB_SCHEME', 'https')),
                'cluster' => env('REVERB_APP_CLUSTER', 'mt1'),
                'user_channel' => 'user.'.($request->header('X-User-Id') ?: 'guest'),
            ],
        ]);
    }

    public function test(Request $request)
    {
        $payload = [
            'type' => 'test',
            'message' => $request->input('message', 'RRR realtime connected'),
            'created_at' => now()->toISOString(),
        ];
        if ($request->filled('user_id')) {
            event(new PrivateRealtimeEvent('user.'.$request->input('user_id'), 'notification.created', $payload));
        } else {
            event(new AppRealtimeEvent('rrr.public', 'app.test', $payload));
        }
        return response()->json(['success' => true, 'sent' => true, 'payload' => $payload]);
    }
}
