# RRR App ↔ Laravel Admin Full Connection Matrix

Every major Flutter page is mapped to Laravel API endpoints and Admin Panel controls. Keep `API_BASE_URL=https://your-domain.com/api/v1` in release builds.

| Flutter Page | Admin Panel Control | App API Endpoints | Admin URL |
|---|---|---|---|
| Splash/Login/Register | Auth Management | `/firebase-login, /auth/complete-profile, /auth/me, /real-id-login` | `/admin/users` |
| Home | Banners, Events, Rooms, Rankings | `/home, /banners, /events, /rooms, /rankings/top-hosts` | `/admin/banners, /admin/events, /admin/rooms, /admin/host-rankings` |
| Rooms / Voice Room | Rooms, Agora, Layouts, Gifts, Chat, Settings | `/rooms, /agora/token, /room-layouts, /rooms/{id}/settings, /rooms/{id}/messages, /rooms/{id}/gifts/send` | `/admin/rooms, /admin/agora-settings, /admin/gifts` |
| Moment | Moments Admin | `/moments, /moments/{id}/like, /moments/{id}/comment` | `/admin/moments` |
| Messages | Messages + System Messages | `/messages/conversations, /messages/{id}, /messages/send, /system-messages` | `/admin/customer-service, /admin/notifications` |
| Profile | Users, Profile Edit, Photos | `/profile, /profile/update, /auth/me` | `/admin/users` |
| Wallet / Payment | Recharge, Wallet, Withdraw | `/wallet, /recharge/packages, /recharge/create, /withdrawals/request, /withdrawals/my` | `/admin/payment-settings, /admin/recharge-requests, /admin/withdraws` |
| VIP / Store | VIP plans + inventory | `/store/vip-plans, /store/vip-plans/buy, /store/inventory` | `/admin/vip-plans, /admin/live/{collection}/store` |
| Agency Center | Agency Host Invites | `/agency/hosts, /agency/host-invites/request, /agency/host-invites/verify` | `/admin/agencies` |
| BD Center | BD Agent Invites | `/bd/agents, /bd/agent-invites/request, /bd/agent-invites/verify` | `/admin/team/bd` |
| Verification | User/Host KYC | `/user-verification/status, /user-verification/submit, /host/verification/status, /host/verification/submit` | `/admin/verification-center` |
| Host Task | 7 Day Host Task | `/host/tasks/current, /host/tasks/progress, /host/tasks/settle` | `/admin/host-task-system` |
| Customer Service | Support Tickets | `/support/tickets, /support/tickets/{id}/messages` | `/admin/customer-service` |
| Notifications | FCM + Broadcast | `/update-fcm-token, /notifications, /system-messages` | `/admin/notifications` |
| Families | Families + Ranking | `/families, /families/ranking, /families/{id}/level-up` | `/admin/families` |
| Reports/Safety | Reports, Ban, Blacklist | `/reports, /admin/reports, /admin/blacklist` | `/admin/reports, /admin/settings` |
| Analytics | Dashboard, Logs, Security | `/admin/dashboard/analytics, /admin/activity-logs, /admin/security/settings` | `/admin, /admin/settings` |


## Production Rule
- Flutter pages must not use demo/local data in release.
- Admin panel is the source for banners, events, VIP plans, gifts, room layouts, recharge packages, families, tasks, store items and security settings.
- Realtime updates use private user channel `user.{user_id}` and room channel `room.{room_id}`.
- FCM is used when the app is background/closed.

## Build
```bash
flutter build appbundle --release --dart-define=API_BASE_URL=https://your-domain.com/api/v1 --dart-define=REALTIME_ENABLED=true
```
