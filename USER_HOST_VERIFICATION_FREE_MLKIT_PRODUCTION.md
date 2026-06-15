# User + Host Verification: Free ML Kit + Manual Admin Review

Launch mode uses Flutter Google ML Kit face detection only as a basic client-side quality check. It does not auto-approve users or hosts.

## User Verification Flow
1. User opens Verify Account in Flutter.
2. User captures front-camera selfie.
3. Flutter ML Kit checks exactly one clear face.
4. Flutter uploads selfie, gender, and country to Laravel: `POST /api/v1/user-verification/submit`.
5. Laravel stores request in MongoDB collection `user_verifications` with `status=pending_review`.
6. Admin opens Admin Panel > Verification Center > User Verification.
7. Admin approves/rejects.
8. On approve, user document gets:
   - `is_verified=true`
   - `verified_badge=blue`
   - `user_verification_status=verified`

## Host Verification Flow
1. User/host opens Host Face Verification in Flutter.
2. User captures front-camera selfie.
3. Flutter ML Kit checks exactly one clear face.
4. Flutter uploads selfie to Laravel: `POST /api/v1/host/verification/submit`.
5. Laravel stores request in MongoDB collection `host_verifications` with `status=pending_review`.
6. Admin opens Admin Panel > Verification Center > Host Verification.
7. Admin approves/rejects.
8. On approve:
   - user role becomes `host`
   - `isHost=true`
   - host record status becomes `active`
   - `host_verification_status=approved`

## Admin Panel URLs
- `/admin/verification-center`
- `/admin/verifications/users`
- `/admin/verifications/hosts`

## Future AWS Upgrade
Later replace manual review with AWS Rekognition Face Liveness score checks:
- User verification: score >= 75
- Host verification: score >= 85

Until AWS is connected, do not auto-approve just because ML Kit detected a face.
