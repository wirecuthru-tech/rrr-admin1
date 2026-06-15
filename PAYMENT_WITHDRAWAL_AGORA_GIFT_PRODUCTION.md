
# RRR Payment + Withdrawal + Agora + Gift Production Phase

## Admin Panel Controls
- Payment Management
  - Razorpay key id / secret
  - UPI/Paytm QR ID, merchant name, QR image URL
  - Coins per rupee
  - Minimum recharge amount
  - Enable/disable gateway
- Coin Packages
  - Add/edit package price and coins
  - Sort order and status
- Recharge Requests
  - Manual UPI/Paytm UTR approval
  - Approved requests credit coins and wallet transaction
- Withdrawal Management
  - Pending/approved/rejected requests
  - UPI / bank account details
  - Admin note and export-ready collections
- Gift Management
  - Add/edit animated gifts
  - Price, animation URL/Lottie key, status
- Agora Settings
  - App ID, certificate, token expiry
  - Voice/video room token endpoint

## Flutter App Controls
- Recharge wallet: Razorpay checkout + UPI QR manual flow
- Withdraw page: UPI/bank withdrawal request
- Voice room: Agora mic/speaker/mute/leave support
- Room gifts: animated gifts via API + realtime room broadcast
- Room messages: realtime room chat via Reverb

## Important Production ENV
```env
RAZORPAY_KEY_ID=
RAZORPAY_KEY_SECRET=
AGORA_APP_ID=
AGORA_APP_CERTIFICATE=
BROADCAST_CONNECTION=reverb
```

## Collections
- recharge_packages
- recharge_requests
- wallet
- wallet_transactions
- withdrawals
- animated_gifts
- room_messages
- room_gifts
- room_settings
- pk_battles
