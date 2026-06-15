# RRR System Message Production Module

System messages are one-way app messages from Laravel to a single user. They are saved in MongoDB and also broadcast realtime on the user channel.

## Channel rule

Every user receives private realtime messages on:

```text
user.{user_id}
```

Example:

```text
user.155462
```

Only that user should subscribe to this channel from Flutter.

## MongoDB collection

```text
system_messages
```

Fields:

```json
{
  "user_id": "155462",
  "type": "otp",
  "title": "Your OTP Code",
  "body": "Your OTP is 123456",
  "data": {},
  "is_read": false,
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

## Supported types

| Type | When to send | Title example |
|---|---|---|
| `otp` | Login/Register or Agency/BD invite OTP | Your OTP Code |
| `host_verified` | Admin verifies host | Host Verified ✅ |
| `agency_join` | User added to agency | Welcome to Safal Agency |
| `agency_kick` | User removed from agency | Removed from Agency |
| `gift_received` | Offline gift received | You received a gift |
| `follow` | New follower | New Follower |
| `ban` | Account banned | Account Suspended |
| `warning` | Policy/language warning | Warning: Language |
| `level_up` | User level increased | Level 15 Reached 🎉 |
| `support_reply` | Customer service reply | Customer Service Reply |

## Laravel helper

`AppController` and `AdminController` now include:

```php
sendSystemMsg($userId, $type, $title, $body, $data = []);
```

It does 2 things:

1. Saves to MongoDB `system_messages`
2. Broadcasts realtime on `user.{user_id}` with event `system.message`

## APIs

```text
GET  /api/v1/system-messages
POST /api/v1/system-messages/read
POST /api/v1/admin/system-messages/send
```

Admin send body:

```json
{
  "user_id": "155462",
  "type": "warning",
  "title": "Warning: Language",
  "body": "Please follow community rules.",
  "data": {}
}
```

## Customer Service vs System Message

Customer Service = normal chat/ticket between user and admin.

System Message = important one-way Laravel alert, popup + list save.
