# Host 7 Day Task System - Production Contract

## Business rule
- Only verified/approved hosts get the free 7 day task.
- Day 1 to Day 7: host must complete 2 hours live (120 minutes) and receive 5 calls per day.
- If all 7 daily tasks are complete, host gets 5000 reward coins.
- Host target for 7 days is 115000.
- If 115000 target is completed, system creates an automatic withdrawal request.
- If 115000 target is not completed when 7 days expire, withdrawal demand becomes 0.

## MongoDB collections
- `host_seven_day_tasks`
- `host_task_progress_logs`
- `withdraws`
- `wallet_transactions`

## Flutter APIs
- `GET /api/v1/host/tasks/current`
- `POST /api/v1/host/tasks/progress`
- `POST /api/v1/host/tasks/settle`

## Production integration notes
- Live minutes should be updated by Agora room session end event.
- Calls received should be updated by video/audio call accept/end event.
- Earnings/target progress should be updated from gifts, call charges, agency earnings, or host beans depending on final business logic.
- Manual progress fields in Flutter are for testing/admin/debug flow and can be hidden from normal host UI after backend events are connected.
