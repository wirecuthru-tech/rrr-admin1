# Host Face Verification Production Flow

Added APIs:

- `GET /api/v1/host/verification/status`
- `POST /api/v1/host/verification/submit` multipart field `selfie`
- `POST /api/v1/admin/hosts/{userId}/verification/review`

Flutter flow:

1. Host opens **Profile → Host Verification**.
2. Front camera opens through `image_picker`.
3. Flutter ML Kit checks that one clear face is present.
4. Selfie uploads to Laravel.
5. Laravel stores the file in `storage/app/public/host_verifications`.
6. If client face check passed, user is auto-marked as host and host status becomes `active`.

MongoDB collections used:

- `host_verifications`
- `hosts`
- `users`

Important production note:

The included implementation is a basic face-presence check. It can detect that a face exists in the selfie. Strong anti-spoofing/liveness against printed photos, screen replay, masks, and deepfakes requires connecting a real liveness provider such as AWS Rekognition Face Liveness, FaceTec, Google Identity verification, or another KYC/liveness SDK.

Deployment checklist:

```bash
php artisan storage:link
```

Make sure your Render/Laravel domain serves `/storage/...` URLs.
