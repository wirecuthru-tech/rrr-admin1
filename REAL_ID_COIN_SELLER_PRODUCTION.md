# Real ID + Coin Seller Production System

## Real ID rule
- Internal MongoDB ObjectId and Firebase UID stay hidden.
- Public Real ID starts at `555555` and increments by 1 for every new user.
- Real ID is used in app/admin search, profile, messages, agency/BD add, live rooms, gifts, customer service and system messages.

## Coin Seller roles
- Normal: can sell coins, center visible, no public recharge list, no withdrawal.
- Medium: can sell coins, appears in recharge coin seller list, notification shows seller name/mobile/Real ID/amount/bio.
- Super: can sell coins, appears in list, profile tag visible, can request withdrawal.

## Admin APIs
- GET `/api/v1/admin/coin-sellers`
- POST `/api/v1/admin/coin-sellers`
- POST `/api/v1/admin/coin-sellers/{realId}/coins`
- POST `/api/v1/admin/coin-sellers/{realId}/status`

## App APIs
- GET `/api/v1/coin-sellers/me`
- GET `/api/v1/coin-sellers/list`
- POST `/api/v1/coin-sellers/transfer`
- POST `/api/v1/coin-sellers/withdrawals/request`
- GET `/api/v1/users/search?q=555555`

## API check result
Static route/controller check: all declared API controller methods exist in the patched Laravel source. Live API still must be tested after deploy with `php artisan route:list` and app runtime logs.
