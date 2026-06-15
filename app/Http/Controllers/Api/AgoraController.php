<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgoraController extends Controller
{
    public function token(Request $request)
    {
        $data = $request->validate([
            'channel_name' => 'required|string',
            'uid' => 'nullable|integer',
            'role' => 'nullable|string',
            'type' => 'nullable|in:voice,video',
        ]);

        $settings = json_decode(json_encode(DB::connection('mongodb')->table('settings')->first() ?? []), true);
        $appId = $settings['agora_app_id'] ?? env('AGORA_APP_ID', '');
        $certificate = $settings['agora_app_certificate'] ?? env('AGORA_APP_CERTIFICATE', '');
        $expiry = (int)($settings['agora_token_expiry'] ?? 3600);

        // Production note: install an Agora token builder package or add Agora's official PHP token builder here.
        // This API shape is final; Flutter never stores App Certificate.
        $token = $certificate ? hash('sha256', $appId.'|'.$certificate.'|'.$data['channel_name'].'|'.($data['uid'] ?? 0).'|'.(time()+$expiry)) : null;

        return response()->json([
            'success' => true,
            'app_id' => $appId,
            'channel_name' => $data['channel_name'],
            'uid' => $data['uid'] ?? random_int(10000, 999999),
            'token' => $token,
            'expires_in' => $expiry,
        ]);
    }
}
