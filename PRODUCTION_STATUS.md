# RRR Laravel Admin/API Production Status

Active backend flow:

- Laravel API serves Flutter app data
- MongoDB Atlas is the primary app database
- Firebase UID/ID token comes from Flutter
- Agora App ID/App Certificate must be stored in admin/settings or `.env`
- Admin panel manages app data, banners, rooms, gifts, VIP, events, settings

Before production release you must configure:

1. `.env` MongoDB Atlas connection:
   - `DB_CONNECTION=mongodb`
   - `DB_URI=mongodb+srv://...`
   - `DB_DATABASE=...`
2. Firebase Admin verification:
   - Add Firebase Admin SDK package or Google JWT verification middleware.
   - Do not trust only `X-User-Id` in production.
3. Agora production token:
   - Replace placeholder token hash in `app/Http/Controllers/Api/AgoraController.php` with Agora official PHP token builder.
   - Keep App Certificate only in Laravel/admin, never in Flutter.
4. Payments:
   - Add Razorpay/PhonePe/Paytm server-side order create + webhook verification.
5. FCM:
   - Add Firebase service account to backend and send notifications from Laravel jobs.
6. Queue/cron:
   - Configure queue worker for notifications, events, ranking resets, VIP expiry.
7. Hosting:
   - Use HTTPS domain for API, not localhost.

Security warning:

Current package is a live API scaffold with MongoDB CRUD and wallet safety checks. For true production release, Firebase token verification, official Agora token builder, payment webhooks, and server hardening must be completed with real credentials.
