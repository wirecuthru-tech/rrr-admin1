<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\AgencyController;

// Public
Route::get('/', function () {
    return view('welcome');
});

// Admin Panel - Sab ek hi Controller se
Route::prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Users - MongoDB live
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/view/{id}', [AdminController::class, 'userView'])->name('user.view');
    Route::delete('/users/delete/{id}', [AdminController::class, 'userDelete'])->name('user.delete');
    
    // Rooms - LIVE
    Route::get('/rooms', [AdminController::class, 'rooms'])->name('rooms');
    
    // Gifts - LIVE
    Route::get('/gifts', [AdminController::class, 'gifts'])->name('gifts');
    Route::get('/gifts/create', [AdminController::class, 'giftCreate'])->name('gift.create');
    Route::post('/gifts/store', [AdminController::class, 'giftStore'])->name('gift.store');
    
    // Hosts - LIVE
    Route::get('/hosts', [AdminController::class, 'hosts'])->name('hosts');
    Route::get('/hosts/view/{id}', [AdminController::class, 'hostView'])->name('host.view');
    
    // Host Applications - PENDING WALE
    Route::get('/hosts/applications', [AdminController::class, 'hostApplications'])->name('hosts.applications');
    Route::post('/hosts/approve/{id}', [AdminController::class, 'hostApprove'])->name('host.approve');
    Route::post('/hosts/reject/{id}', [AdminController::class, 'hostReject'])->name('host.reject');
    
    // Host Rankings - TOP BY DIAMONDS
    Route::get('/hosts/rankings', [AdminController::class, 'hostRankings'])->name('hosts.rankings');
    
    // Baaki static - baad me live karenge
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::get('/withdraws', [AdminController::class, 'withdraws'])->name('withdraws');
    Route::get('/notifications', [AdminController::class, 'notifications'])->name('notifications');
    Route::get('/agencies', [AdminController::class, 'agencies'])->name('agencies');
    Route::get('/agencies/create', [AdminController::class, 'agencyCreate'])->name('agencies.create');
    Route::get('/banners', [AdminController::class, 'banners'])->name('banners');
    Route::get('/recharge-packages', [AdminController::class, 'rechargePackages'])->name('recharge-packages');
    Route::get('/vip-plans', [AdminController::class, 'vipPlans'])->name('vip-plans');
    Route::get('/levels', [AdminController::class, 'levels'])->name('levels');
    Route::get('/tasks', [AdminController::class, 'tasks'])->name('tasks');
    Route::get('/games', [AdminController::class, 'games'])->name('games');
});

Route::prefix('admin/agencies')->group(function () {

    Route::get(
        '/',
        [AgencyController::class,'index']
    )->name('agency.index');

    Route::get(
        '/create',
        [AgencyController::class,'create']
    )->name('agency.create');

    Route::post(
        '/store',
        [AgencyController::class,'store']
    )->name('agency.store');

    Route::get(
        '/view/{id}',
        [AgencyController::class,'view']
    )->name('agency.view');

    Route::get(
        '/edit/{id}',
        [AgencyController::class,'edit']
    )->name('agency.edit');

    Route::post(
        '/update/{id}',
        [AgencyController::class,'update']
    )->name('agency.update');

    Route::get(
        '/delete/{id}',
        [AgencyController::class,'delete']
    )->name('agency.delete');

});