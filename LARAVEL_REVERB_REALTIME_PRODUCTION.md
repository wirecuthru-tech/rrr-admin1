# RRR Laravel Reverb / WebSocket Production Setup

Realtime is added for app-open live updates. FCM is still used when the app is closed.

## Install / deploy

```bash
composer install
php artisan reverb:install
php artisan config:clear
php artisan route:clear
```

## .env

```env
BROADCAST_CONNECTION=reverb
RRR_REALTIME_ENABLED=true
REVERB_APP_ID=rrr_app
REVERB_APP_KEY=change_me
REVERB_APP_SECRET=change_me_secret
REVERB_HOST=0.0.0.0
REVERB_PORT=8080
REVERB_SCHEME=http
REVERB_PUBLIC_HOST=your-render-domain.com
REVERB_PUBLIC_PORT=443
REVERB_PUBLIC_SCHEME=https
REVERB_APP_CLUSTER=mt1
```

## Run Reverb

Local:
```bash
php artisan reverb:start --host=0.0.0.0 --port=8080
```

On production, run Reverb as a separate Render service/process or worker. Expose it with HTTPS/WSS.

## Events broadcasted

- `room.created` on `rrr.public`
- `room.user.joined` / `room.user.left` on `room.{roomId}`
- `gift.sent` on `room.{roomId}`
- `gift.received` on `user.{userId}`
- `message.sent` on `user.{userId}`
- `notification.created` on `user.{userId}`
- `moment.created` on `rrr.public`
- `host.task.updated` on `user.{userId}`

## Flutter endpoint

`GET /api/v1/realtime/config` returns the app key/host/port/scheme used by Flutter.

## Test

```bash
curl -X POST https://your-domain.com/api/v1/realtime/test \
  -H 'Content-Type: application/json' \
  -d '{"message":"hello realtime"}'
```
