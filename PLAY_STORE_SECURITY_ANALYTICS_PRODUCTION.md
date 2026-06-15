# RRR Final Launch Phase

## Added APIs
- `GET /api/v1/launch/checklist`
- `GET /api/v1/admin/dashboard/analytics`
- `GET /api/v1/admin/activity-logs`
- `GET|POST /api/v1/admin/security/settings`
- `POST /api/v1/client/crash-report`

## Production Security Checklist
- Enable HTTPS only.
- Set `APP_DEBUG=false`.
- Set strong `APP_KEY`.
- Store Razorpay/Agora/Firebase secrets in environment only.
- Protect admin APIs with admin/manager/superadmin role middleware.
- Enable rate limit for login, payment, withdrawal, and report APIs.
- Keep MongoDB Atlas IP allowlist locked to server IP.
- Run backup script daily with cron.

## Reverb Production
Use Supervisor/systemd for:
```bash
php artisan reverb:start --host=0.0.0.0 --port=8080
```

## MongoDB Backup
```bash
MONGODB_URI="mongodb+srv://..." ./scripts/mongodb_backup.sh
```
