# Final Admin Connection Checklist

Use these tests after deployment:

1. Build Flutter with `API_BASE_URL=https://your-domain.com/api/v1`.
2. Open `/api/v1/app-pages/connections` and verify JSON loads.
3. Open Admin Panel `/admin/app-pages-connection`.
4. Add Banner/Event/Family/VIP/Gift/Layout/Recharge Package from Admin.
5. Refresh app Home/Profile/Store/Room screens and confirm data appears.
6. Send Customer Service reply from Admin and confirm app receives system message.
7. Verify Reverb/FCM for realtime + background notifications.
8. Run `php artisan route:list` and check all `/api/v1/*` routes exist.

If any page shows empty, add data from the matching Admin URL in `ALL_PAGES_ADMIN_CONNECTION_MATRIX.md`.
