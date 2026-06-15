# RRR Final Complete Production API

Added final APIs for VIP Plan Store, app store inventory, room layout store, 5 voice room layouts, room settings, room chat, gifts, and admin price control.

Base URL: `/api/v1`

Important endpoints:
- GET `/production/summary`
- GET `/store/vip-plans`
- POST `/store/vip-plans/buy`
- GET `/room-layouts`
- POST `/room-layouts/buy`
- GET `/room-layouts/my`
- GET/POST `/rooms/{roomId}/settings`
- GET/POST `/rooms/{roomId}/messages`
- POST `/rooms/{roomId}/gifts/send`
- GET/POST `/admin/store/{type}` where type is `vip`, `layout`, `frame`, `bubble`, `entry`, `badge`.

Realtime events are broadcast on `room.{roomId}` and `user.{userId}` channels.
