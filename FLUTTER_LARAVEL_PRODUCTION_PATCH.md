# RRR Flutter + Laravel Production Patch

This backend ZIP was patched to match the current Flutter production flows.

## Added/Aligned API Endpoints

Base path: `/api/v1`

### Auth / Profile
- `POST /firebase-login`
- `POST /complete-profile`
- `POST /auth/complete-profile`
- `GET /auth/me`
- `POST /real-id-login`
- `POST /auth/login-id`
- `POST /update-fcm-token`

### Messages
- `GET /messages/conversations`
- `GET /messages/{realIdOrFirebaseUid}`
- `POST /messages/{realIdOrFirebaseUid}/send`
- `POST /messages/send`

### Moments
- `GET /moments`
- `POST /moments` with `caption/text`, `media_url`, or multipart `media`
- `POST /moments/{id}/like`
- `POST /moments/{id}/comment`

### Agency Center
- `GET /agency/hosts`
- `POST /agency/host-invites/request`
- `POST /agency/host-invites/verify`

### BD Center
- `GET /bd/agents`
- `POST /bd/agent-invites/request`
- `POST /bd/agent-invites/verify`

## Important Production Notes

- OTP for Agency/BD flows is saved in MongoDB and delivered to the target user through the app notifications/inbox collection, not SMS.
- 6-digit Real ID is generated on Laravel side with duplicate check in MongoDB.
- Release Flutter build must use HTTPS API URL:

```bash
flutter build apk --release --dart-define=API_BASE_URL=https://your-render-domain.com/api/v1
```

## MongoDB Collections Used

- `users`
- `wallet`
- `messages`
- `conversations`
- `moments`
- `moment_likes`
- `moment_comments`
- `notifications`
- `agency_host_invites`
- `agency_hosts`
- `bd_agent_invites`
- `bd_agents`

## Required Server Setup

- Configure `.env` MongoDB Atlas connection.
- Run `php artisan storage:link` for uploaded moment media.
- Ensure Render/hosting domain is HTTPS.
- Configure Firebase project in Flutter and Firebase Console.
