# User Basic KYC Verification API

Base: `/api/v1`

## GET `/user-verification/status`
Headers: `X-User-Id` or Firebase bearer token mapped to user.

Returns latest verification status: `not_submitted`, `pending_review`, `verified`, `rejected`.

## POST `/user-verification/submit`
Multipart form-data:
- `selfie`: jpg/png/webp selfie from front camera
- `gender`: Male/Female/Other
- `country`: user country
- `client_face_check`: `passed` only after Flutter ML Kit detects one clear face

Current auto-approval rule: `client_face_check=passed` => `verified`.
For stronger production liveness/deepfake protection, connect AWS Rekognition Face Liveness, FaceTec, Onfido, Sumsub, or equivalent provider before auto-approval.

MongoDB collections:
- `user_verifications`
- `users` fields updated: `user_verification_status`, `verification_status`, `is_verified`, `verified_badge`, `verification_selfie_url`, `verified_at`

## POST `/admin/users/{userId}/verification/review`
Body:
```json
{ "status": "verified", "reason": "optional" }
```
Use for admin manual approve/reject.
