<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Realtime Broadcast Channels
|--------------------------------------------------------------------------
| Laravel Reverb uses the Pusher protocol. Flutter subscribes to these
| channels after login. Keep authorization strict in production.
*/

Broadcast::channel('user.{userId}', function ($user = null, string $userId) {
    // This project accepts Firebase UID via API headers. If Sanctum/Firebase
    // auth middleware is enabled later, restrict this to the authenticated UID.
    return true;
});

Broadcast::channel('room.{roomId}', function ($user = null, string $roomId) {
    return true;
});

Broadcast::channel('agency.{agencyId}', function ($user = null, string $agencyId) {
    return true;
});

Broadcast::channel('bd.{bdId}', function ($user = null, string $bdId) {
    return true;
});
