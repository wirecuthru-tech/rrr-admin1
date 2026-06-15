<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class AppPageConnectionController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'api_base' => '/api/v1',
            'pages' => [
                ['page' => 'Splash/Login/Register', 'admin' => 'Auth Management', 'api' => '/firebase-login, /auth/complete-profile, /auth/me, /real-id-login', 'admin_url' => '/admin/users'],
                ['page' => 'Home', 'admin' => 'Banners, Events, Rooms, Rankings', 'api' => '/home, /banners, /events, /rooms, /rankings/top-hosts', 'admin_url' => '/admin/banners, /admin/events, /admin/rooms, /admin/host-rankings'],
                ['page' => 'Rooms / Voice Room', 'admin' => 'Rooms, Agora, Layouts, Gifts, Chat, Settings', 'api' => '/rooms, /agora/token, /room-layouts, /rooms/{id}/settings, /rooms/{id}/messages, /rooms/{id}/gifts/send', 'admin_url' => '/admin/rooms, /admin/agora-settings, /admin/gifts'],
                ['page' => 'Moment', 'admin' => 'Moments Admin', 'api' => '/moments, /moments/{id}/like, /moments/{id}/comment', 'admin_url' => '/admin/moments'],
                ['page' => 'Messages', 'admin' => 'Messages + System Messages', 'api' => '/messages/conversations, /messages/{id}, /messages/send, /system-messages', 'admin_url' => '/admin/customer-service, /admin/notifications'],
                ['page' => 'Profile', 'admin' => 'Users, Profile Edit, Photos', 'api' => '/profile, /profile/update, /auth/me', 'admin_url' => '/admin/users'],
                ['page' => 'Wallet / Payment', 'admin' => 'Recharge, Wallet, Withdraw', 'api' => '/wallet, /recharge/packages, /recharge/create, /withdrawals/request, /withdrawals/my', 'admin_url' => '/admin/payment-settings, /admin/recharge-requests, /admin/withdraws'],
                ['page' => 'VIP / Store', 'admin' => 'VIP plans + inventory', 'api' => '/store/vip-plans, /store/vip-plans/buy, /store/inventory', 'admin_url' => '/admin/vip-plans, /admin/live/{collection}/store'],
                ['page' => 'Agency Center', 'admin' => 'Agency Host Invites', 'api' => '/agency/hosts, /agency/host-invites/request, /agency/host-invites/verify', 'admin_url' => '/admin/agencies'],
                ['page' => 'BD Center', 'admin' => 'BD Agent Invites', 'api' => '/bd/agents, /bd/agent-invites/request, /bd/agent-invites/verify', 'admin_url' => '/admin/team/bd'],
                ['page' => 'Verification', 'admin' => 'User/Host KYC', 'api' => '/user-verification/status, /user-verification/submit, /host/verification/status, /host/verification/submit', 'admin_url' => '/admin/verification-center'],
                ['page' => 'Host Task', 'admin' => '7 Day Host Task', 'api' => '/host/tasks/current, /host/tasks/progress, /host/tasks/settle', 'admin_url' => '/admin/host-task-system'],
                ['page' => 'Customer Service', 'admin' => 'Support Tickets', 'api' => '/support/tickets, /support/tickets/{id}/messages', 'admin_url' => '/admin/customer-service'],
                ['page' => 'Notifications', 'admin' => 'FCM + Broadcast', 'api' => '/update-fcm-token, /notifications, /system-messages', 'admin_url' => '/admin/notifications'],
                ['page' => 'Families', 'admin' => 'Families + Ranking', 'api' => '/families, /families/ranking, /families/{id}/level-up', 'admin_url' => '/admin/families'],
                ['page' => 'Reports/Safety', 'admin' => 'Reports, Ban, Blacklist', 'api' => '/reports, /admin/reports, /admin/blacklist', 'admin_url' => '/admin/reports, /admin/settings'],
                ['page' => 'Analytics', 'admin' => 'Dashboard, Logs, Security', 'api' => '/admin/dashboard/analytics, /admin/activity-logs, /admin/security/settings', 'admin_url' => '/admin, /admin/settings'],
            ],
        ]);
    }
}
