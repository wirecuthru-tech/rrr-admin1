<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Admin\AgencyController;
use App\Http\Controllers\TeamController;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])
    ->name('admin.login');

Route::post('/admin/login', [AdminAuthController::class, 'login'])
    ->name('admin.login.submit');

Route::post('/admin/logout', [AdminAuthController::class, 'logout'])
    ->name('admin.logout');

Route::prefix('admin')
    ->name('admin.')
    ->middleware('admin.owner')
    ->group(function () {

        Route::get('/', [AdminController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/verification-center', [AdminController::class, 'verificationCenter'])
            ->name('verification.center');

        Route::get('/verifications/users', [AdminController::class, 'userVerifications'])
            ->name('verifications.users');

        Route::get('/verifications/hosts', [AdminController::class, 'hostVerifications'])
            ->name('verifications.hosts');

        Route::post('/verifications/users/{id}/review', [AdminController::class, 'reviewUserVerificationWeb'])
            ->name('verifications.users.review');

        Route::post('/verifications/hosts/{id}/review', [AdminController::class, 'reviewHostVerificationWeb'])
            ->name('verifications.hosts.review');

        Route::get('/users', [AdminController::class, 'users'])
            ->name('users');

        Route::get('/users/view/{id}', [AdminController::class, 'userView'])
            ->name('user.view');

        Route::delete('/users/delete/{id}', [AdminController::class, 'userDelete'])
            ->name('user.delete');

        Route::get('/users/delete/{id}', [AdminController::class, 'userDelete'])
            ->name('user.delete.get');

        Route::get('/rooms', [AdminController::class, 'rooms'])
            ->name('rooms');

        Route::get('/hosts', [AdminController::class, 'hosts'])
            ->name('hosts');

        Route::get('/hosts/view/{id}', [AdminController::class, 'hostView'])
            ->name('host.view');

        Route::get('/host-applications', [AdminController::class, 'hostApplications'])
            ->name('host-applications');

        Route::get('/hosts/applications', [AdminController::class, 'hostApplications'])
            ->name('hosts.applications');

        Route::post('/host-approve/{id}', [AdminController::class, 'hostApprove'])
            ->name('host.approve');

        Route::post('/host-reject/{id}', [AdminController::class, 'hostReject'])
            ->name('host.reject');

        Route::post('/hosts/approve/{id}', [AdminController::class, 'hostApprove'])
            ->name('hosts.approve');

        Route::post('/hosts/reject/{id}', [AdminController::class, 'hostReject'])
            ->name('hosts.reject');

        Route::get('/host-rankings', [AdminController::class, 'hostRankings'])
            ->name('host-rankings');

        Route::get('/hosts/rankings', [AdminController::class, 'hostRankings'])
            ->name('hosts.rankings');

        Route::get('/host-salaries', [AdminController::class, 'hostSalaries'])
            ->name('host-salaries');

        Route::get('/host-withdraws', [AdminController::class, 'hostWithdraws'])
            ->name('host-withdraws');


        Route::get('/host-task-system', [AdminController::class, 'hostTaskSystem'])
            ->name('host-task-system');

        Route::post('/host-task-system/{id}/settle', [AdminController::class, 'hostTaskSettleWeb'])
            ->name('host-task-system.settle');

        Route::get('/gifts', [AdminController::class, 'gifts'])
            ->name('gifts');

        Route::get('/gifts/create', [AdminController::class, 'giftCreate'])
            ->name('gift.create');

        Route::post('/gifts/store', [AdminController::class, 'giftStore'])
            ->name('gift.store');

        Route::get('/reports', [AdminController::class, 'reports'])
            ->name('reports');


        Route::get('/app-pages-connection', [AdminController::class, 'appPagesConnection'])
            ->name('app-pages.connection');

        Route::get('/settings', [AdminController::class, 'settings'])
            ->name('settings');

        Route::post('/settings/update', [AdminController::class, 'updateSettings'])
            ->name('settings.update');

        Route::get('/withdraws', [AdminController::class, 'withdraws'])
            ->name('withdraws');

        Route::get('/notifications', [AdminController::class, 'notifications'])
            ->name('notifications');

        Route::get('/customer-service', [AdminController::class, 'customerServiceTickets'])
            ->name('customer-service');

        Route::get('/customer-service/{id}', [AdminController::class, 'customerServiceTicketView'])
            ->name('customer-service.view');

        Route::post('/customer-service/{id}/reply', [AdminController::class, 'customerServiceReply'])
            ->name('customer-service.reply');

        Route::post('/customer-service/{id}/status', [AdminController::class, 'customerServiceStatus'])
            ->name('customer-service.status');

        Route::get('/banners', [AdminController::class, 'banners'])
            ->name('banners');

        Route::get('/recharge-packages', [AdminController::class, 'rechargePackages'])
            ->name('recharge-packages');

        Route::get('/vip-plans', [AdminController::class, 'vipPlans'])
            ->name('vip-plans');

        Route::get('/levels', [AdminController::class, 'levels'])
            ->name('levels');

        Route::get('/tasks', [AdminController::class, 'tasks'])
            ->name('tasks');

        Route::get('/games', [AdminController::class, 'games'])
            ->name('games');

        Route::get('/agencies', [AdminController::class, 'agencies'])
            ->name('agencies');

        Route::get('/agencies/create', [AgencyController::class, 'create'])
            ->name('agency.create');

        Route::post('/agencies/store', [AgencyController::class, 'store'])
            ->name('agency.store');

        Route::get('/agencies/view/{id}', [AgencyController::class, 'view'])
            ->name('agency.view');

        Route::get('/agencies/edit/{id}', [AgencyController::class, 'edit'])
            ->name('agency.edit');

        Route::post('/agencies/update/{id}', [AgencyController::class, 'update'])
            ->name('agency.update');

        Route::get('/agencies/delete/{id}', [AgencyController::class, 'delete'])
            ->name('agency.delete');


        Route::get('/agora-settings', [AdminController::class, 'agoraSettings'])->name('agora.settings');
        Route::get('/video-calls', [AdminController::class, 'videoCalls'])->name('video.calls');
        Route::get('/moments', [AdminController::class, 'momentsAdmin'])->name('moments');
        Route::get('/families', [AdminController::class, 'familiesAdmin'])->name('families');
        Route::get('/pk-battles', [AdminController::class, 'pkBattlesAdmin'])->name('pk.battles');
        Route::get('/events', [AdminController::class, 'eventsAdmin'])->name('events');


        Route::get('/rankings-live', [AdminController::class, 'rankingsAdmin'])->name('rankings.live');
        Route::get('/family-chat', [AdminController::class, 'familyChatAdmin'])->name('family.chat');
        Route::get('/voice-reels', [AdminController::class, 'voiceReelsAdmin'])->name('voice.reels');
        Route::get('/stories', [AdminController::class, 'storiesAdmin'])->name('stories');
        Route::get('/podcasts', [AdminController::class, 'podcastsAdmin'])->name('podcasts');
        Route::get('/country-war', [AdminController::class, 'countryWarAdmin'])->name('country.war');
        Route::get('/marketplace', [AdminController::class, 'marketplaceAdmin'])->name('marketplace');
        Route::get('/creator-shop', [AdminController::class, 'creatorShopAdmin'])->name('creator.shop');
        Route::get('/missions', [AdminController::class, 'missionsAdmin'])->name('missions');
        Route::get('/stories-reels', [AdminController::class, 'storiesReelsAdmin'])->name('stories.reels');
        Route::get('/ai-center', [AdminController::class, 'aiCenter'])->name('ai.center');
        Route::post('/ai-center/update', [AdminController::class, 'updateAiSettings'])->name('ai.center.update');
        Route::get('/payment-settings', [AdminController::class, 'paymentSettings'])->name('payment.settings');
        Route::post('/payment-settings/update', [AdminController::class, 'updatePaymentSettings'])->name('payment.settings.update');
        Route::get('/recharge-requests', [AdminController::class, 'rechargeRequests'])->name('recharge.requests');
        Route::post('/recharge-requests/{id}/action', [AdminController::class, 'rechargeRequestAction'])->name('recharge.requests.action');

        Route::get('/coin-sellers', [AdminController::class, 'coinSellers'])->name('coin-sellers');
        Route::post('/coin-sellers/store', [AdminController::class, 'coinSellerStore'])->name('coin-sellers.store');
        Route::post('/coin-sellers/{realId}/coins', [AdminController::class, 'coinSellerCoins'])->name('coin-sellers.coins');
        Route::post('/coin-sellers/{realId}/status', [AdminController::class, 'coinSellerStatus'])->name('coin-sellers.status');


        Route::post('/live/{collection}/store', [AdminController::class, 'storeGeneric'])
            ->name('live.store');

        Route::delete('/live/{collection}/delete/{id}', [AdminController::class, 'deleteGeneric'])
            ->name('live.delete');

        /*
        |--------------------------------------------------------------------------
        | Team Hierarchy / Multi Post / Country Teams
        |--------------------------------------------------------------------------
        */

        Route::get('/team/{role}', [TeamController::class, 'index'])
            ->name('team.index');

        Route::get('/team/{role}/create', [TeamController::class, 'create'])
            ->name('team.create');

        Route::post('/team/{role}/store', [TeamController::class, 'store'])
            ->name('team.store');

        Route::get('/team/post/{id}', [TeamController::class, 'show'])
            ->name('team.show');

        Route::post('/team/post/{id}/suspend', [TeamController::class, 'suspend'])
            ->name('team.suspend');

        Route::get('/country-teams', [TeamController::class, 'countryTeams'])
            ->name('country.teams');

        Route::get('/country-teams/{country}', [TeamController::class, 'countryView'])
            ->name('country.view');

        Route::get('/team-tree/{post_id}', [TeamController::class, 'teamTree'])
            ->name('team.tree');
    });
