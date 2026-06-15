# RRR Free AI Production Modules

Added modules without paid voice-to-voice APIs:

- AI Moderator: rule-based abuse/spam detection with logs.
- Subtitle Translation: free dictionary/fallback text subtitle translation. Full voice-to-voice can be added later with paid STT/TTS provider.
- AI Matchmaking: country/language/level scoring.
- AI Event Host: scripted event host messages for welcome, quiz, gift and winner.
- AI Content Recommendations: ranked rooms, hosts, events and voice reels.

Admin page: `/admin/ai-center`
API routes: `/api/v1/ai/*`
Collections: `ai_moderation_logs`, `ai_translation_logs`, `ai_matchmaking_logs`, `ai_event_host_logs`, `ai_recommendation_logs`.
