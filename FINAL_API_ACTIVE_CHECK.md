# Final API Connection Check

Static check date: generated in sandbox.

## Laravel API routes
- API route controller references found: 160
- Missing controller methods: 0
- Result: PASS

## Flutter endpoint strings found
- `/agency/host-invites/request`
- `/agency/host-invites/verify`
- `/agency/hosts`
- `/agora/token`
- `/ai-host/message`
- `/ai/event-host/run`
- `/ai/matchmaking`
- `/ai/moderator/check`
- `/ai/recommendations`
- `/ai/translate/subtitle`
- `/animated-gifts`
- `/app-pages/connections`
- `/auth/complete-profile`
- `/auth/login-id`
- `/auth/me`
- `/banners`
- `/bd/agent-invites/request`
- `/bd/agent-invites/verify`
- `/bd/agents`
- `/checkin/daily`
- `/client/crash-report`
- `/coin-sellers/list`
- `/coin-sellers/me`
- `/coin-sellers/transfer`
- `/coin-sellers/withdrawals/request`
- `/complete-profile`
- `/country-war`
- `/creator-shop`
- `/customer-service`
- `/daily-checkin`
- `/events`
- `/families`
- `/families/ranking`
- `/firebase-login`
- `/full-version`
- `/gifts`
- `/home`
- `/host/tasks/current`
- `/host/tasks/progress`
- `/host/tasks/settle`
- `/host/verification/status`
- `/host/verification/submit`
- `/launch/checklist`
- `/level`
- `/login`
- `/lucky-spin/config`
- `/lucky-spin/play`
- `/marketplace`
- `/messages`
- `/messages/conversations`
- `/messages/new`
- `/messages/send`
- `/moments`
- `/moments/create`
- `/notifications`
- `/payment`
- `/payment/config`
- `/payment/paytm-qr/create`
- `/payment/paytm-qr/submit-utr`
- `/payment/razorpay/create-order`
- `/payment/razorpay/verify`
- `/pk/battles/end`
- `/pk/battles/score`
- `/pk/battles/start`
- `/pk/start`
- `/podcasts`
- `/production/summary`
- `/profile`
- `/profile/update`
- `/rankings/host`
- `/rankings/rich`
- `/rankings/rich-list`
- `/rankings/top-hosts`
- `/realtime/config`
- `/recharge/create`
- `/recharge/packages`
- `/referrals/apply`
- `/referrals/summary`
- `/reports`
- `/room-layouts`
- `/room-layouts/buy`
- `/rooms`
- `/settings`
- `/signup`
- `/splash`
- `/store/inventory`
- `/store/vip-plans`
- `/store/vip-plans/buy`
- `/stories`
- `/support/tickets`
- `/system-messages`
- `/system-messages/read`
- `/task-center`
- `/task-center/claim`
- `/tasks`
- `/update-fcm-token`
- `/user-verification/status`
- `/user-verification/submit`
- `/users/search`
- `/video-call/end`
- `/video-call/request`
- `/video-call/respond`
- `/vip-plans`
- `/vip/buy`
- `/voice-reels`
- `/wallet`
- `/wallet/transactions`
- `/withdraw`
- `/withdrawals/my`
- `/withdrawals/request`

## Important note
This is a static code check. Live production check still requires deploying Laravel and running:

```bash
php artisan route:list
```

Then build Flutter with:

```bash
flutter build appbundle --release --dart-define=API_BASE_URL=https://your-domain.com/api/v1
```

## Real ID / Coin Seller additions verified in code
- Real ID sequential generator starts at `555555`.
- `users/search` supports search by Real ID.
- `coin-sellers` app/admin APIs are declared and implemented.
- Profile shows dynamic multi-role golden SVG badges.
- Coin Seller Center only shows for users with coin seller status.
