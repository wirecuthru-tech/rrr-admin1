<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AppController;
use App\Http\Controllers\Api\AgoraController;
use App\Http\Controllers\Api\RealtimeController;
use App\Http\Controllers\Api\RrrCompleteProductionController;
use App\Http\Controllers\Api\AppPageConnectionController;

Route::prefix('v1')->group(function () {
    Route::get('/app-pages/connections', [AppPageConnectionController::class, 'index']);
    Route::post('/firebase-login', [AppController::class, 'firebaseLogin']);

    Route::get('/home', [AppController::class, 'home']);
    Route::get('/settings', [AppController::class, 'settings']);

    Route::get('/rooms', [AppController::class, 'rooms']);
    Route::post('/rooms', [AppController::class, 'createRoom']);
    Route::post('/rooms/{id}/join', [AppController::class, 'joinRoom']);
    Route::post('/rooms/{id}/leave', [AppController::class, 'leaveRoom']);

    Route::get('/gifts', [AppController::class, 'gifts']);
    Route::post('/gifts/send', [AppController::class, 'sendGift']);

    Route::get('/wallet', [AppController::class, 'wallet']);
    Route::get('/profile/{id?}', [AppController::class, 'profile']);
    Route::post('/profile/update', [AppController::class, 'updateProfile']);

    Route::get('/moments', [AppController::class, 'moments']);
    Route::post('/moments', [AppController::class, 'createMoment']);

    Route::get('/families', [AppController::class, 'families']);
    Route::get('/rankings/{type?}', [AppController::class, 'rankings']);
    Route::get('/vip-plans', [AppController::class, 'vipPlans']);
    Route::get('/recharge-packages', [AppController::class, 'rechargePackages']);
    Route::get('/payment/config', [AppController::class, 'paymentConfig']);
    Route::post('/payment/paytm-qr/create', [AppController::class, 'createPaytmQrRecharge']);
    Route::post('/payment/paytm-qr/submit-utr', [AppController::class, 'submitPaytmUtr']);
    Route::post('/payment/razorpay/create-order', [AppController::class, 'createRazorpayOrder']);
    Route::post('/payment/razorpay/verify', [AppController::class, 'verifyRazorpayPayment']);
    Route::get('/banners', [AppController::class, 'banners']);
    Route::get('/events', [AppController::class, 'events']);
    Route::get('/games', [AppController::class, 'games']);


    // Realtime / Laravel Reverb WebSocket APIs
    Route::get('/realtime/config', [RealtimeController::class, 'config']);
    Route::post('/realtime/test', [RealtimeController::class, 'test']);

    Route::post('/agora/token', [AgoraController::class, 'token']);
    Route::post('/video-call/request', [AppController::class, 'requestVideoCall']);
    Route::post('/video-call/respond', [AppController::class, 'respondVideoCall']);
    Route::post('/video-call/end', [AppController::class, 'endVideoCall']);

    // V2 - V5 full social audio platform APIs
    Route::get('/family/{id}', [AppController::class, 'familyDetail']);
    Route::post('/families', [AppController::class, 'createFamily']);
    Route::post('/families/{id}/join', [AppController::class, 'joinFamily']);
    Route::post('/family-chat/send', [AppController::class, 'sendFamilyChat']);

    Route::post('/vip/buy', [AppController::class, 'buyVip']);
    Route::post('/pk/start', [AppController::class, 'startPkBattle']);
    Route::post('/pk/score', [AppController::class, 'updatePkScore']);
    Route::post('/pk/end', [AppController::class, 'endPkBattle']);

    Route::post('/events/{id}/join', [AppController::class, 'joinEvent']);
    Route::get('/notifications', [AppController::class, 'appNotifications']);
    Route::post('/notifications/read', [AppController::class, 'readNotifications']);

    Route::get('/system-messages', [AppController::class, 'systemMessages']);
    Route::post('/system-messages/read', [AppController::class, 'readSystemMessages']);
    Route::post('/admin/system-messages/send', [AppController::class, 'adminSendSystemMessage']);

    Route::get('/voice-reels', [AppController::class, 'voiceReels']);
    Route::post('/voice-reels', [AppController::class, 'createVoiceReel']);
    Route::get('/stories', [AppController::class, 'stories']);
    Route::post('/stories', [AppController::class, 'createStory']);
    Route::get('/podcasts', [AppController::class, 'podcasts']);
    Route::post('/podcasts', [AppController::class, 'createPodcast']);
    Route::get('/country-war', [AppController::class, 'countryWar']);
    Route::post('/ai-host/message', [AppController::class, 'aiHostMessage']);

    Route::get('/marketplace', [AppController::class, 'marketplace']);
    Route::post('/marketplace/buy', [AppController::class, 'buyMarketplaceItem']);
    Route::get('/creator-shop/{host_id?}', [AppController::class, 'creatorShop']);
    Route::post('/creator-subscribe', [AppController::class, 'creatorSubscribe']);
    Route::post('/daily-checkin', [AppController::class, 'dailyCheckin']);
    Route::post('/missions/complete', [AppController::class, 'completeMission']);

    Route::post('/payment/razorpay/webhook', [AppController::class, 'razorpayWebhook']);


    // Free / low-cost AI production modules
    Route::get('/ai/moderator/settings', [AppController::class, 'aiModeratorSettings']);
    Route::post('/ai/moderator/check', [AppController::class, 'aiModeratorCheck']);
    Route::post('/ai/translate/subtitle', [AppController::class, 'aiSubtitleTranslate']);
    Route::get('/ai/matchmaking', [AppController::class, 'aiMatchmaking']);
    Route::post('/ai/event-host/run', [AppController::class, 'aiEventHostRun']);
    Route::get('/ai/recommendations', [AppController::class, 'aiRecommendations']);


    // Production auth/profile/FCM APIs used by Flutter
    Route::post('/complete-profile', [AppController::class, 'completeProfile']);
    Route::post('/real-id-login', [AppController::class, 'realIdLogin']);

    Route::post('/auth/complete-profile', [AppController::class, 'completeProfile']);
    Route::post('/auth/login-id', [AppController::class, 'realIdLogin']);
    Route::get('/auth/me', [AppController::class, 'authMe']);
    Route::post('/update-fcm-token', [AppController::class, 'updateFcmToken']);

    // Production Messages APIs
    Route::get('/messages/conversations', [AppController::class, 'conversations']);
    Route::get('/messages/{withUser}', [AppController::class, 'chatMessages']);
    Route::post('/messages/{withUser}/send', [AppController::class, 'sendMessageTo']);
    Route::post('/messages/send', [AppController::class, 'sendMessage']);

    // Production Moments APIs
    Route::post('/moments/{id}/like', [AppController::class, 'likeMoment']);
    Route::post('/moments/{id}/comment', [AppController::class, 'commentMoment']);

    // Production Agency/BD Center APIs with in-app OTP
    Route::get('/agency/hosts', [AppController::class, 'agencyHosts']);
    Route::post('/agency/host-invites/request', [AppController::class, 'agencyHostInviteRequest']);
    Route::post('/agency/host-invites/verify', [AppController::class, 'agencyHostInviteVerify']);
    Route::get('/bd/agents', [AppController::class, 'bdAgents']);
    Route::post('/bd/agent-invites/request', [AppController::class, 'bdAgentInviteRequest']);
    Route::post('/bd/agent-invites/verify', [AppController::class, 'bdAgentInviteVerify']);



    // User basic KYC verification APIs
    Route::get('/user-verification/status', [AppController::class, 'userVerificationStatus']);
    Route::post('/user-verification/submit', [AppController::class, 'submitUserVerification']);
    Route::post('/admin/users/{userId}/verification/review', [AppController::class, 'reviewUserVerification']);



    // Host 7-day free task + withdrawal target APIs
    Route::get('/host/tasks/current', [AppController::class, 'hostCurrentTask']);
    Route::post('/host/tasks/progress', [AppController::class, 'hostTaskProgress']);
    Route::post('/host/tasks/settle', [AppController::class, 'hostTaskSettle']);

    // Host face verification / auto approval APIs
    Route::get('/host/verification/status', [AppController::class, 'hostVerificationStatus']);
    Route::post('/host/verification/submit', [AppController::class, 'submitHostVerification']);
    Route::post('/admin/hosts/{userId}/verification/review', [AppController::class, 'reviewHostVerification']);

    // Customer Service / Support Ticket APIs
    Route::get('/support/tickets', [AppController::class, 'supportTickets']);
    Route::post('/support/tickets', [AppController::class, 'createSupportTicket']);
    Route::get('/support/tickets/{ticketId}/messages', [AppController::class, 'supportTicketMessages']);
    Route::post('/support/tickets/{ticketId}/messages', [AppController::class, 'sendSupportTicketMessage']);


    // FINAL COMPLETE PRODUCTION APIs: store, VIP, voice room layouts, gifts, chat and settings
    Route::get('/production/summary', [RrrCompleteProductionController::class, 'productionSummary']);
    Route::get('/store/categories', [RrrCompleteProductionController::class, 'storeCategories']);
    Route::get('/store/inventory', [RrrCompleteProductionController::class, 'inventory']);
    Route::get('/store/vip-plans', [RrrCompleteProductionController::class, 'vipPlans']);
    Route::post('/store/vip-plans/buy', [RrrCompleteProductionController::class, 'buyVipPlan']);
    Route::get('/room-layouts', [RrrCompleteProductionController::class, 'roomLayouts']);
    Route::post('/room-layouts/buy', [RrrCompleteProductionController::class, 'buyRoomLayout']);
    Route::get('/room-layouts/my', [RrrCompleteProductionController::class, 'myRoomLayouts']);
    Route::get('/rooms/{roomId}/settings', [RrrCompleteProductionController::class, 'roomSettings']);
    Route::post('/rooms/{roomId}/settings', [RrrCompleteProductionController::class, 'updateRoomSettings']);
    Route::get('/rooms/{roomId}/messages', [RrrCompleteProductionController::class, 'roomMessages']);
    Route::post('/rooms/{roomId}/messages', [RrrCompleteProductionController::class, 'sendRoomMessage']);
    Route::post('/rooms/{roomId}/gifts/send', [RrrCompleteProductionController::class, 'sendRoomGift']);
    Route::get('/admin/store/{type}', [RrrCompleteProductionController::class, 'adminStoreList']);
    Route::post('/admin/store/{type}', [RrrCompleteProductionController::class, 'adminStoreUpsert']);


    // NEXT PRODUCTION REVENUE + ENGAGEMENT + SAFETY APIs (all admin configurable)
    Route::get('/recharge/packages', [RrrCompleteProductionController::class, 'rechargePackagesV2']);
    Route::post('/recharge/create', [RrrCompleteProductionController::class, 'createRecharge']);
    Route::get('/wallet/transactions', [RrrCompleteProductionController::class, 'walletTransactions']);
    Route::post('/withdrawals/request', [RrrCompleteProductionController::class, 'requestWithdrawal']);
    Route::get('/withdrawals/my', [RrrCompleteProductionController::class, 'myWithdrawals']);
    Route::post('/admin/withdrawals/{id}/review', [RrrCompleteProductionController::class, 'adminReviewWithdrawal']);

    Route::get('/animated-gifts', [RrrCompleteProductionController::class, 'animatedGifts']);
    Route::post('/admin/animated-gifts', [RrrCompleteProductionController::class, 'adminAnimatedGiftUpsert']);
    Route::get('/pk/battles/{roomId}', [RrrCompleteProductionController::class, 'pkStatus']);
    Route::post('/pk/battles/start', [RrrCompleteProductionController::class, 'pkStartV2']);
    Route::post('/pk/battles/score', [RrrCompleteProductionController::class, 'pkScoreV2']);
    Route::post('/pk/battles/end', [RrrCompleteProductionController::class, 'pkEndV2']);

    Route::post('/checkin/daily', [RrrCompleteProductionController::class, 'dailyCheckinV2']);
    Route::get('/referrals/summary', [RrrCompleteProductionController::class, 'referralSummary']);
    Route::post('/referrals/apply', [RrrCompleteProductionController::class, 'applyReferral']);
    Route::get('/lucky-spin/config', [RrrCompleteProductionController::class, 'luckySpinConfig']);
    Route::post('/lucky-spin/play', [RrrCompleteProductionController::class, 'luckySpinPlay']);
    Route::get('/task-center', [RrrCompleteProductionController::class, 'taskCenter']);
    Route::post('/task-center/claim', [RrrCompleteProductionController::class, 'taskClaim']);

    Route::post('/reports', [RrrCompleteProductionController::class, 'createReport']);
    Route::get('/admin/reports', [RrrCompleteProductionController::class, 'adminReports']);
    Route::post('/admin/reports/{id}/action', [RrrCompleteProductionController::class, 'adminReportAction']);
    Route::post('/admin/blacklist', [RrrCompleteProductionController::class, 'adminBlacklist']);

    Route::get('/families/ranking', [RrrCompleteProductionController::class, 'familyRankingV2']);
    Route::post('/families/{id}/level-up', [RrrCompleteProductionController::class, 'familyLevelUp']);
    Route::get('/rankings/rich-list', [RrrCompleteProductionController::class, 'richListV2']);
    Route::get('/rankings/top-hosts', [RrrCompleteProductionController::class, 'topHostsV2']);

    Route::get('/admin/recharge-packages', [RrrCompleteProductionController::class, 'adminRechargePackages']);
    Route::post('/admin/recharge-packages', [RrrCompleteProductionController::class, 'adminRechargePackageUpsert']);
    Route::get('/admin/tasks', [RrrCompleteProductionController::class, 'adminTasks']);
    Route::post('/admin/tasks', [RrrCompleteProductionController::class, 'adminTaskUpsert']);
    Route::get('/admin/spin-prizes', [RrrCompleteProductionController::class, 'adminSpinPrizes']);
    Route::post('/admin/spin-prizes', [RrrCompleteProductionController::class, 'adminSpinPrizeUpsert']);

    // FINAL LAUNCH / PLAY STORE / SECURITY / ANALYTICS APIs
    Route::get('/launch/checklist', [RrrCompleteProductionController::class, 'launchChecklist']);
    Route::get('/admin/dashboard/analytics', [RrrCompleteProductionController::class, 'adminDashboardAnalytics']);
    Route::get('/admin/activity-logs', [RrrCompleteProductionController::class, 'adminActivityLogs']);
    Route::match(['get','post'], '/admin/security/settings', [RrrCompleteProductionController::class, 'adminSecuritySettings']);
    Route::post('/client/crash-report', [RrrCompleteProductionController::class, 'reportClientCrash']);


    // Real ID + Coin Seller production APIs
    Route::get('/users/search', [AppController::class, 'searchUsersByRealId']);
    Route::get('/coin-sellers/list', [AppController::class, 'coinSellerList']);
    Route::get('/coin-sellers/me', [AppController::class, 'coinSellerMe']);
    Route::post('/coin-sellers/transfer', [AppController::class, 'coinSellerTransfer']);
    Route::post('/coin-sellers/withdrawals/request', [AppController::class, 'coinSellerWithdrawalRequest']);
    Route::get('/admin/coin-sellers', [AppController::class, 'adminCoinSellers']);
    Route::post('/admin/coin-sellers', [AppController::class, 'adminCreateCoinSeller']);
    Route::post('/admin/coin-sellers/{realId}/coins', [AppController::class, 'adminCoinSellerCoins']);
    Route::post('/admin/coin-sellers/{realId}/status', [AppController::class, 'adminCoinSellerStatus']);

});
