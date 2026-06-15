# RRR Laravel Admin/API Live Implementation

This build supports:

- MongoDB Atlas as the app database.
- Firebase Auth UID sync through `/api/v1/firebase-login`.
- FCM token stored on users.
- Live app APIs for rooms, gifts, wallet, profile, VIP, ranking, family, PK, events, notifications, stories, voice reels, podcasts, country war, marketplace, creator shop, missions and daily check-in.
- Admin panel pages for the same modules.
- Agora settings stay in admin/settings/MongoDB; Flutter never stores App Certificate.

## Configure `.env`

```env
DB_CONNECTION=mongodb
MONGODB_DSN=mongodb+srv://USERNAME:PASSWORD@CLUSTER.mongodb.net/rrr_admin?retryWrites=true&w=majority
AGORA_APP_ID=your_agora_app_id
AGORA_APP_CERTIFICATE=your_agora_certificate
```

## Install and run

```bash
composer install
php artisan key:generate
php artisan db:seed
php artisan serve
```

## Important

`/api/v1/agora/token` has the final backend-token API shape. For real production calls, replace the hash placeholder in `app/Http/Controllers/Api/AgoraController.php` with Agora's official PHP RTC token builder package/class.

