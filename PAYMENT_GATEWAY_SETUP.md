# Payment Gateway Setup

Added payment scaffold:

## Admin Panel
- `/admin/payment-settings`
  - Paytm QR enable/disable
  - Paytm UPI ID
  - Paytm QR image URL
  - Razorpay Key ID / Secret / Webhook Secret
  - coins per rupee
  - minimum recharge amount
- `/admin/recharge-requests`
  - Paytm QR manual recharge requests
  - approve/reject
  - approved requests credit coins to user wallet

## API
- `GET /api/v1/payment/config`
- `POST /api/v1/payment/paytm-qr/create`
- `POST /api/v1/payment/paytm-qr/submit-utr`
- `POST /api/v1/payment/razorpay/create-order`
- `POST /api/v1/payment/razorpay/verify`
- `POST /api/v1/payment/razorpay/webhook`

## Flutter
- Wallet page has Recharge button
- New payment screen supports Paytm QR/manual UTR flow
- Razorpay backend order API is ready. Flutter checkout SDK can be enabled after keys are added.

## Collections
- `recharge_requests`
- `wallet_transactions`
- `payment_webhook_logs`
