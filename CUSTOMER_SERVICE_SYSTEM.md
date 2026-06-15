# RRR Customer Service System

## Flutter App
Added Customer Service page where user can:
- Create support ticket/problem message
- Select category: Account, Recharge, Withdraw, Room, Host, Agency, Bug, Other
- Select priority: normal, high, urgent
- View ticket list
- Open ticket chat
- Send follow-up messages
- Read admin replies

## Flutter Routes
- `/customer-service`

## Laravel APIs
- `GET /api/v1/support/tickets`
- `POST /api/v1/support/tickets`
- `GET /api/v1/support/tickets/{ticketId}/messages`
- `POST /api/v1/support/tickets/{ticketId}/messages`

## MongoDB Collections
- `support_tickets`
- `support_messages`
- `notifications`

## Admin Panel
Customer Service menu added under App Live Modules.
Admin can:
- See all tickets
- Filter open/answered/closed
- Open ticket conversation
- Reply to user
- Close ticket

## Production Note
Replies are saved in MongoDB and a notification record is created for the user. FCM/Reverb can broadcast these replies when the deployed server is configured.
