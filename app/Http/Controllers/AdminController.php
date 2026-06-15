<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\ObjectId;
use Throwable;
use App\Events\AppRealtimeEvent;

class AdminController extends Controller
{
    private function col(string $name)
    {
        return DB::connection('mongodb')->table($name);
    }

    private function mongoId($id)
    {
        try {
            return new ObjectId($id);
        } catch (Throwable $e) {
            return $id;
        }
    }

    private function arr($item)
    {
        return json_decode(json_encode($item), true);
    }


    private function sendSystemMsg(string $userId, string $type, string $title, string $body, array $data = []): array
    {
        $userId = trim($userId);
        if ($userId === '') return [];
        $message = [
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data,
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $id = $this->col('system_messages')->insertGetId($message);
        $message['_id'] = (string) $id;
        try { event(new AppRealtimeEvent('user.'.$userId, 'system.message', ['system_message' => $message])); } catch (Throwable $e) { report($e); }
        try { event(new AppRealtimeEvent('user.'.$userId, 'notification.created', ['notification' => $message])); } catch (Throwable $e) { report($e); }
        return $message;
    }

    private function rows(string $collection)
    {
        return $this->col($collection)
            ->orderBy('_id', 'desc')
            ->get()
            ->map(fn($item) => $this->arr($item));
    }

    public function dashboard()
    {
        $totalUsers = $this->col('users')->count();
        $totalHosts = $this->col('hosts')->count();
        $totalRooms = $this->col('rooms')->count();
        $totalGifts = $this->col('gifts')->count();

        $activeRooms = $this->col('rooms')->where('status', 'active')->count();
        $giftsSent = $this->col('gift_logs')->count();
        $reports = $this->col('reports')->count();

        $recentActivities = $this->rows('activity_logs')->take(10);

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalHosts',
            'totalRooms',
            'totalGifts',
            'activeRooms',
            'giftsSent',
            'reports',
            'recentActivities'
        ));
    }

    public function users()
    {
        $users = $this->rows('users');
        return view('admin.users', compact('users'));
    }

    public function userView($id)
    {
        $user = $this->col('users')->where('_id', $this->mongoId($id))->first();

        if (!$user) {
            return redirect()->route('admin.users')->with('error', 'User nahi mila');
        }

        $user = $this->arr($user);
        $userId = $user['_id'] ?? $user['id'] ?? $id;

        $roomsJoined = $this->col('room_logs')->where('user_id', $userId)->count();
        $roomsCreated = $this->col('rooms')->where('created_by', $userId)->count();
        $giftsSent = $this->col('gift_logs')->where('sender_id', $userId)->count();
        $giftsReceived = $this->col('gift_logs')->where('receiver_id', $userId)->count();

        return view('admin.user-view', compact(
            'user',
            'roomsJoined',
            'roomsCreated',
            'giftsSent',
            'giftsReceived'
        ));
    }

    public function userDelete($id)
    {
        $this->col('users')->where('_id', $this->mongoId($id))->delete();
        return redirect()->route('admin.users')->with('success', 'User delete ho gaya');
    }



    public function verificationCenter()
    {
        $pendingUsers = $this->col('user_verifications')->where('status', 'pending_review')->count();
        $approvedUsers = $this->col('user_verifications')->where('status', 'verified')->count();
        $rejectedUsers = $this->col('user_verifications')->where('status', 'rejected')->count();
        $pendingHosts = $this->col('host_verifications')->where('status', 'pending_review')->count();
        $approvedHosts = $this->col('host_verifications')->where('status', 'approved')->count();
        $rejectedHosts = $this->col('host_verifications')->where('status', 'rejected')->count();
        return view('admin.verification-center', compact('pendingUsers','approvedUsers','rejectedUsers','pendingHosts','approvedHosts','rejectedHosts'));
    }

    public function userVerifications(Request $request)
    {
        $status = $request->query('status', 'pending_review');
        $query = $this->col('user_verifications')->orderBy('_id', 'desc');
        if ($status !== 'all') $query->where('status', $status);
        $verifications = $query->limit(300)->get()->map(fn($item) => $this->arr($item));
        return view('admin.verifications-user', compact('verifications', 'status'));
    }

    public function hostVerifications(Request $request)
    {
        $status = $request->query('status', 'pending_review');
        $query = $this->col('host_verifications')->orderBy('_id', 'desc');
        if ($status !== 'all') $query->where('status', $status);
        $verifications = $query->limit(300)->get()->map(fn($item) => $this->arr($item));
        return view('admin.verifications-host', compact('verifications', 'status'));
    }

    public function reviewUserVerificationWeb(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|in:verified,rejected,pending_review',
            'reason' => 'nullable|string|max:500',
        ]);
        $verification = $this->col('user_verifications')->where('_id', $this->mongoId($id))->first();
        if (!$verification) return back()->with('error', 'Verification request nahi mili');
        $verification = $this->arr($verification);
        $uid = $verification['user_id'] ?? '';
        $status = $data['status'];
        $update = [
            'status' => $status,
            'message' => $status === 'verified' ? 'Verified by admin.' : ($data['reason'] ?? 'Reviewed by admin.'),
            'reviewed_at' => now(),
            'updated_at' => now(),
        ];
        if ($status === 'verified') $update['verified_at'] = now();
        if ($status === 'rejected') $update['rejection_reason'] = $data['reason'] ?? 'Rejected by admin.';
        $this->col('user_verifications')->where('_id', $this->mongoId($id))->update($update);
        $this->col('users')->where('firebase_uid', $uid)->orWhere('userId', $uid)->orWhere('real_id', $uid)->update([
            'user_verification_status' => $status,
            'verification_status' => $status,
            'is_verified' => $status === 'verified',
            'verified_badge' => $status === 'verified' ? 'blue' : '',
            'verified_at' => $status === 'verified' ? now() : null,
            'updated_at' => now(),
        ]);
        return back()->with('success', 'User verification updated: '.$status);
    }

    public function reviewHostVerificationWeb(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|in:approved,rejected,pending_review',
            'reason' => 'nullable|string|max:500',
        ]);
        $verification = $this->col('host_verifications')->where('_id', $this->mongoId($id))->first();
        if (!$verification) return back()->with('error', 'Host verification request nahi mili');
        $verification = $this->arr($verification);
        $uid = $verification['user_id'] ?? '';
        $status = $data['status'];
        $this->col('host_verifications')->where('_id', $this->mongoId($id))->update([
            'status' => $status,
            'message' => $status === 'approved' ? 'Approved by admin.' : ($data['reason'] ?? 'Reviewed by admin.'),
            'reviewed_at' => now(),
            'updated_at' => now(),
        ]);
        $this->col('users')->where('firebase_uid', $uid)->orWhere('userId', $uid)->orWhere('real_id', $uid)->update([
            'role' => $status === 'approved' ? 'host' : 'user',
            'isHost' => $status === 'approved',
            'host_verification_status' => $status,
            'host_verified_at' => $status === 'approved' ? now() : null,
            'updated_at' => now(),
        ]);
        $this->col('hosts')->updateOrInsert(['user_id' => $uid], [
            'user_id' => $uid,
            'status' => $status === 'approved' ? 'active' : $status,
            'verification_status' => $status,
            'selfie_url' => $verification['selfie_url'] ?? '',
            'approved_at' => $status === 'approved' ? now() : null,
            'updated_at' => now(),
        ]);
        return back()->with('success', 'Host verification updated: '.$status);
    }

    public function rooms()
    {
        $rooms = $this->rows('rooms');
        $totalRooms = $rooms->count();
        $activeRooms = $rooms->where('status', 'active')->count();

        return view('admin.rooms', compact('rooms', 'totalRooms', 'activeRooms'));
    }


    public function hostTaskSystem(Request $request)
    {
        $status = $request->query('status', 'all');
        $query = $this->col('host_seven_day_tasks')->orderBy('_id', 'desc');
        if ($status !== 'all') $query->where('status', $status);
        $tasks = $query->limit(500)->get()->map(fn($item) => $this->arr($item));
        $stats = [
            'active' => $this->col('host_seven_day_tasks')->where('status', 'active')->count(),
            'completed' => $this->col('host_seven_day_tasks')->where('status', 'completed')->count(),
            'failed_target' => $this->col('host_seven_day_tasks')->where('status', 'failed_target')->count(),
            'auto_withdrawals' => $this->col('host_seven_day_tasks')->where('auto_withdrawal_created', true)->count(),
        ];
        return view('admin.host-task-system', compact('tasks', 'stats', 'status'));
    }

    public function hostTaskSettleWeb($id)
    {
        $task = $this->col('host_seven_day_tasks')->where('_id', $this->mongoId($id))->first();
        if (!$task) return back()->with('error', 'Host task nahi mila');
        $task = $this->arr($task);
        $uid = $task['user_id'] ?? '';
        $allDailyComplete = true;
        foreach (($task['days'] ?? []) as $d) {
            if (!(bool)($d['completed'] ?? false)) { $allDailyComplete = false; break; }
        }
        $targetComplete = (int)($task['total_earnings'] ?? 0) >= (int)($task['target_amount'] ?? 115000);
        if ($allDailyComplete && (($task['reward_status'] ?? '') !== 'credited')) {
            $reward = (int)($task['reward'] ?? 5000);
            $this->col('wallet')->where('firebase_uid', $uid)->orWhere('user_id', $uid)->increment('coins', $reward);
            $this->col('users')->where('firebase_uid', $uid)->orWhere('userId', $uid)->orWhere('real_id', $uid)->increment('coins', $reward);
            $this->col('wallet_transactions')->insert([
                'user_id'=>$uid,'type'=>'host_task_reward','coins'=>$reward,'status'=>'success','task_id'=>$id,'created_at'=>now()
            ]);
        }
        $update = [
            'daily_completed'=>$allDailyComplete,
            'target_completed'=>$targetComplete,
            'reward_status'=>$allDailyComplete ? 'credited' : ($task['reward_status'] ?? 'pending'),
            'updated_at'=>now(),
        ];
        if ($allDailyComplete && $targetComplete) {
            $exists = $this->col('withdraws')->where('user_id',$uid)->where('task_id',$id)->first();
            if (!$exists) {
                $this->col('withdraws')->insert([
                    'user_id'=>$uid,'task_id'=>$id,'amount'=>(int)($task['total_earnings'] ?? 0),
                    'status'=>'pending','type'=>'host_task_auto_withdrawal','created_at'=>now(),'updated_at'=>now()
                ]);
            }
            $update['status'] = 'completed';
            $update['auto_withdrawal_created'] = true;
            $update['withdrawal_demand'] = (int)($task['total_earnings'] ?? 0);
            $update['completed_at'] = now();
        } else {
            $update['status'] = 'failed_target';
            $update['withdrawal_demand'] = 0;
        }
        $this->col('host_seven_day_tasks')->where('_id', $this->mongoId($id))->update($update);
        return back()->with('success', 'Host task settled');
    }

    public function hosts()
    {
        $hosts = $this->rows('hosts');

        $totalHosts = $hosts->count();
        $activeHosts = $hosts->where('status', 'active')->count();
        $pendingHosts = $hosts->where('status', 'pending')->count();
        $monthlyEarning = $hosts->sum('monthly_earning');

        return view('admin.hosts', compact(
            'hosts',
            'totalHosts',
            'activeHosts',
            'pendingHosts',
            'monthlyEarning'
        ));
    }

    public function hostView($id)
    {
        $host = $this->col('hosts')->where('_id', $this->mongoId($id))->first();

        if ($host) {
            $host = $this->arr($host);
        }

        return view('admin.host-view', compact('host'));
    }

    public function hostApplications()
    {
        $hosts = $this->col('hosts')
            ->where('status', 'pending')
            ->orderBy('_id', 'desc')
            ->get()
            ->map(fn($item) => $this->arr($item));

        return view('admin.host-applications', compact('hosts'));
    }

    public function hostApprove($id)
    {
        $this->col('hosts')->where('_id', $this->mongoId($id))->update([
            'status' => 'active',
            'approved_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Host approve ho gaya!');
    }

    public function hostReject($id)
    {
        $this->col('hosts')->where('_id', $this->mongoId($id))->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Host reject kar diya!');
    }

    public function hostRankings()
    {
        $hosts = $this->col('hosts')
            ->where('status', 'active')
            ->orderBy('diamonds', 'desc')
            ->limit(50)
            ->get()
            ->map(fn($item) => $this->arr($item));

        return view('admin.host-rankings', compact('hosts'));
    }

    public function hostSalaries()
    {
        $salaries = $this->rows('host_salaries');
        return view('admin.host-salaries', compact('salaries'));
    }

    public function hostWithdraws()
    {
        $withdraws = $this->rows('host_withdraws');
        return view('admin.host-withdraws', compact('withdraws'));
    }

    public function gifts()
    {
        $gifts = $this->rows('gifts');
        return view('admin.gifts', compact('gifts'));
    }

    public function giftCreate()
    {
        return view('admin.gift-create');
    }

    public function giftStore(Request $request)
    {
        $request->validate([
            'gift_name' => 'required|string|max:100',
            'gift_price' => 'required|numeric|min:1',
            'gift_category' => 'required|string',
            'gift_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imageName = null;

        if ($request->hasFile('gift_image')) {
            $imageName = time().'_'.$request->gift_image->getClientOriginalName();
            $request->gift_image->move(public_path('uploads/gifts'), $imageName);
        }

        $this->col('gifts')->insert([
            'name' => $request->gift_name,
            'price' => (int) $request->gift_price,
            'category' => $request->gift_category,
            'image' => $imageName,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.gifts')->with('success', 'Gift add ho gaya bhai!');
    }

    public function reports()
    {
        $reports = $this->rows('reports');
        return view('admin.reports', compact('reports'));
    }

    public function settings()
    {
        $settings = $this->arr($this->col('settings')->first() ?? []);
        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->except('_token');

        foreach ($data as $key => $value) {
            if ($value === null) {
                $data[$key] = '';
            }

            if (is_numeric($value)) {
                $data[$key] = $value + 0;
            }
        }

        $data['updated_at'] = now();

        $old = $this->col('settings')->first();

        if ($old && isset($old->_id)) {
            $this->col('settings')->where('_id', $old->_id)->update($data);
        } else {
            $data['created_at'] = now();
            $this->col('settings')->insert($data);
        }

        return back()->with('success', 'Settings update ho gayi!');
    }

    public function withdraws()
    {
        $withdraws = $this->rows('withdraws');
        return view('admin.withdraws', compact('withdraws'));
    }

    public function notifications()
    {
        $notifications = $this->rows('notifications');
        return view('admin.notifications', compact('notifications'));
    }

    public function agencies()
    {
        $agencies = $this->rows('agencies');
        return view('admin.agencies', compact('agencies'));
    }

    public function agencyCreate()
    {
        return view('admin.agency-create');
    }

    public function banners()
    {
        $banners = $this->rows('banners');
        return view('admin.banners', compact('banners'));
    }

    public function rechargePackages()
    {
        $packages = $this->rows('recharge_packages');
        return view('admin.recharge-packages', compact('packages'));
    }

    public function vipPlans()
    {
        $plans = $this->rows('vip_plans');
        return view('admin.vip-plans', compact('plans'));
    }

    public function levels()
    {
        $levels = $this->rows('levels');
        return view('admin.levels', compact('levels'));
    }

    public function tasks()
    {
        $tasks = $this->rows('tasks');
        return view('admin.tasks', compact('tasks'));
    }

    public function games()
    {
        $games = $this->rows('games');
        return view('admin.games', compact('games'));
    }


    public function agoraSettings()
    {
        $settings = $this->arr($this->col('settings')->first() ?? []);
        return view('admin.agora-settings', compact('settings'));
    }

    public function videoCalls()
    {
        $calls = $this->rows('video_calls');
        return view('admin.video-calls', compact('calls'));
    }

    public function momentsAdmin()
    {
        $moments = $this->rows('moments');
        return view('admin.moments', compact('moments'));
    }

    public function familiesAdmin()
    {
        $families = $this->rows('families');
        return view('admin.families', compact('families'));
    }

    public function pkBattlesAdmin()
    {
        $battles = $this->rows('pk_battles');
        return view('admin.pk-battles', compact('battles'));
    }

    public function eventsAdmin()
    {
        $events = $this->rows('events');
        return view('admin.events', compact('events'));
    }

    public function storeGeneric(Request $request, $collection)
    {
        $data = $request->except('_token');

        foreach ($data as $key => $value) {
            if ($value === null) {
                $data[$key] = '';
            }

            if (is_numeric($value)) {
                $data[$key] = $value + 0;
            }
        }

        $data['created_at'] = now();
        $data['updated_at'] = now();

        $this->col($collection)->insert($data);

        return back()->with('success', ucfirst(str_replace('_', ' ', $collection)).' add ho gaya!');
    }

    public function deleteGeneric($collection, $id)
    {
        $this->col($collection)
            ->where('_id', $this->mongoId($id))
            ->delete();

        return back()->with('success', ucfirst(str_replace('_', ' ', $collection)).' delete ho gaya!');
    }

    public function rankingsAdmin(){ $rankings = $this->rows('rankings'); return view('admin.rankings-live', compact('rankings')); }
    public function familyChatAdmin(){ $messages = $this->rows('family_chat'); return view('admin.family-chat', compact('messages')); }
    public function voiceReelsAdmin(){ $reels = $this->rows('voice_reels'); return view('admin.voice-reels', compact('reels')); }
    public function storiesAdmin(){ $stories = $this->rows('stories'); return view('admin.stories', compact('stories')); }
    public function podcastsAdmin(){ $podcasts = $this->rows('podcasts'); return view('admin.podcasts', compact('podcasts')); }
    public function countryWarAdmin(){ $wars = $this->rows('country_wars'); return view('admin.country-war', compact('wars')); }
    public function marketplaceAdmin(){ $items = $this->rows('marketplace_items'); return view('admin.marketplace', compact('items')); }
    public function creatorShopAdmin(){ $items = $this->rows('creator_shop_items'); return view('admin.creator-shop', compact('items')); }
    public function missionsAdmin(){ $missions = $this->rows('missions'); return view('admin.missions', compact('missions')); }
    public function storiesReelsAdmin(){ $stories = $this->rows('stories'); $reels = $this->rows('voice_reels'); return view('admin.stories-reels', compact('stories','reels')); }


    public function aiCenter()
    {
        $settings = $this->arr($this->col('settings')->first() ?? []);
        $moderationLogs = $this->rows('ai_moderation_logs', 30);
        $translationLogs = $this->rows('ai_translation_logs', 20);
        $eventLogs = $this->rows('ai_event_host_logs', 20);
        $recommendationLogs = $this->rows('ai_recommendation_logs', 20);
        return view('admin.ai-center', compact('settings','moderationLogs','translationLogs','eventLogs','recommendationLogs'));
    }

    public function updateAiSettings(Request $request)
    {
        $data = $request->only([
            'ai_moderator_enabled','ai_subtitle_translation_enabled','ai_matchmaking_enabled',
            'ai_event_host_enabled','ai_recommendations_enabled','ai_default_language',
            'ai_auto_warn','ai_auto_mute','ai_auto_kick','ai_blocked_words'
        ]);
        foreach (['ai_moderator_enabled','ai_subtitle_translation_enabled','ai_matchmaking_enabled','ai_event_host_enabled','ai_recommendations_enabled','ai_auto_warn','ai_auto_mute','ai_auto_kick'] as $flag) {
            $data[$flag] = $request->has($flag) ? 1 : 0;
        }
        $data['updated_at'] = now();
        $old = $this->col('settings')->first();
        if ($old && isset($old->_id)) {
            $this->col('settings')->where('_id', $old->_id)->update($data);
        } else {
            $data['created_at'] = now();
            $this->col('settings')->insert($data);
        }
        return back()->with('success', 'AI settings save ho gayi!');
    }


    public function paymentSettings()
    {
        $settings = $this->arr($this->col('settings')->first() ?? []);
        return view('admin.payment-settings', compact('settings'));
    }

    public function updatePaymentSettings(Request $request)
    {
        $data = $request->only([
            'paytm_qr_enabled','paytm_upi_id','paytm_merchant_name','paytm_qr_url','paytm_manual_approval',
            'razorpay_enabled','razorpay_key_id','razorpay_key_secret','razorpay_webhook_secret',
            'coins_per_rupee','min_recharge_amount','payment_mode'
        ]);

        foreach ($data as $key => $value) {
            if ($value === null) $data[$key] = '';
            if (is_numeric($value)) $data[$key] = $value + 0;
        }

        $data['updated_at'] = now();
        $old = $this->col('settings')->first();
        if ($old && isset($old->_id)) {
            $this->col('settings')->where('_id', $old->_id)->update($data);
        } else {
            $data['created_at'] = now();
            $this->col('settings')->insert($data);
        }
        return back()->with('success', 'Payment settings save ho gayi!');
    }

    public function rechargeRequests()
    {
        $requests = $this->rows('recharge_requests');
        return view('admin.recharge-requests', compact('requests'));
    }

    public function rechargeRequestAction(Request $request, $id)
    {
        $action = $request->input('action');
        $row = $this->col('recharge_requests')->where('_id', $this->mongoId($id))->first();
        if (!$row) return back()->with('error', 'Recharge request nahi mili');

        $status = $action === 'approve' ? 'approved' : 'rejected';
        $this->col('recharge_requests')->where('_id', $this->mongoId($id))->update([
            'status' => $status,
            'reviewed_at' => now(),
            'updated_at' => now(),
        ]);

        if ($status === 'approved') {
            $coins = (int) ($row->coins ?? 0);
            $uid = $row->firebase_uid ?? $row->user_id ?? null;
            if ($uid && $coins > 0) {
                $this->col('wallet')->where('firebase_uid', $uid)->increment('coins', $coins);
                $this->col('users')->where('firebase_uid', $uid)->increment('coins', $coins);
                $this->col('wallet_transactions')->insert([
                    'user_id' => $uid,
                    'type' => 'recharge',
                    'gateway' => $row->gateway ?? 'manual',
                    'amount' => (float) ($row->amount ?? 0),
                    'coins' => $coins,
                    'status' => 'success',
                    'reference_id' => (string) $id,
                    'created_at' => now(),
                ]);
            }
        }

        return back()->with('success', 'Recharge request '.$status.' ho gayi!');
    }


    public function customerServiceTickets(Request $request)
    {
        $status = $request->query('status', 'all');
        $query = $this->col('support_tickets')->orderBy('_id', 'desc');
        if ($status !== 'all') $query->where('status', $status);
        $tickets = $query->limit(500)->get()->map(fn($item) => $this->arr($item));
        $stats = [
            'open' => $this->col('support_tickets')->where('status', 'open')->count(),
            'answered' => $this->col('support_tickets')->where('status', 'answered')->count(),
            'closed' => $this->col('support_tickets')->where('status', 'closed')->count(),
            'urgent' => $this->col('support_tickets')->where('priority', 'urgent')->count(),
        ];
        return view('admin.customer-service', compact('tickets', 'stats', 'status'));
    }

    public function customerServiceTicketView($id)
    {
        $ticket = $this->col('support_tickets')->where('_id', $this->mongoId($id))->first();
        if (!$ticket) return redirect()->route('admin.customer-service')->with('error', 'Ticket nahi mila');
        $ticket = $this->arr($ticket);
        $messages = $this->col('support_messages')->where('ticket_id', (string)$id)->orderBy('_id', 'asc')->get()->map(fn($item) => $this->arr($item));
        $this->col('support_tickets')->where('_id', $this->mongoId($id))->update(['unread_admin'=>0, 'updated_at'=>now()]);
        return view('admin.customer-service-view', compact('ticket', 'messages', 'id'));
    }

    public function customerServiceReply(Request $request, $id)
    {
        $data = $request->validate(['message' => 'required|string|max:3000']);
        $ticket = $this->col('support_tickets')->where('_id', $this->mongoId($id))->first();
        if (!$ticket) return back()->with('error', 'Ticket nahi mila');
        $message = [
            'ticket_id' => (string)$id,
            'user_id' => $ticket->user_id ?? '',
            'sender_id' => 'admin',
            'sender_type' => 'admin',
            'message' => $data['message'],
            'read_by_user' => false,
            'read_by_admin' => true,
            'created_at' => now(),
        ];
        $this->col('support_messages')->insert($message);
        $this->col('support_tickets')->where('_id', $this->mongoId($id))->update([
            'status' => 'answered',
            'last_message' => $data['message'],
            'last_sender_type' => 'admin',
            'unread_user' => (int)($ticket->unread_user ?? 0) + 1,
            'updated_at' => now(),
        ]);
        $this->sendSystemMsg(
            (string)($ticket->user_id ?? ''),
            'support_reply',
            'Customer Service Reply',
            $data['message'],
            ['ticket_id'=>(string)$id]
        );
        return back()->with('success', 'Reply user ko bhej diya');
    }

    public function customerServiceStatus(Request $request, $id)
    {
        $data = $request->validate(['status' => 'required|in:open,answered,closed']);
        $this->col('support_tickets')->where('_id', $this->mongoId($id))->update(['status' => $data['status'], 'updated_at' => now()]);
        return back()->with('success', 'Ticket status update ho gaya');
    }

    public function appPagesConnection()
    {
        $pages = [
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
        ];
        return view('admin.app_pages_connection', compact('pages'));
    }


    public function coinSellers(Request $request)
    {
        $query = $this->col('coin_sellers')->orderBy('_id', 'desc');
        if ($request->query('type')) $query->where('seller_type', $request->query('type'));
        if ($request->query('status')) $query->where('status', $request->query('status'));
        $sellers = $query->limit(500)->get()->map(fn($item) => $this->arr($item));
        return view('admin.coin-sellers', compact('sellers'));
    }

    public function coinSellerStore(Request $request)
    {
        $data = $request->validate([
            'real_id' => 'required|string',
            'mobile' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'seller_type' => 'required|in:normal,medium,super',
            'seller_name' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:300',
            'initial_coins' => 'nullable|integer|min:0',
        ]);
        $user = $this->col('users')->where('real_id', $data['real_id'])->orWhere('userId', $data['real_id'])->orWhere('appId', $data['real_id'])->first();
        if (!$user) return back()->with('error', 'User Real ID nahi mila');
        $realId = (string)(($user->real_id ?? null) ?: ($user->userId ?? null) ?: ($user->appId ?? $data['real_id']));
        $payload = [
            'user_id' => (string)($user->_id ?? ''),
            'real_id' => $realId,
            'seller_type' => $data['seller_type'],
            'seller_name' => $data['seller_name'] ?: ($user->name ?? 'Coin Seller'),
            'mobile' => $data['mobile'],
            'whatsapp' => $data['whatsapp'] ?: $data['mobile'],
            'bio' => $data['bio'] ?? '',
            'coin_balance' => (int)($data['initial_coins'] ?? 0),
            'can_sell' => true,
            'can_withdraw' => $data['seller_type'] === 'super',
            'show_public' => in_array($data['seller_type'], ['medium','super']),
            'show_tag' => true,
            'status' => 'active',
            'updated_at' => now(),
        ];
        $exists = $this->col('coin_sellers')->where('real_id', $realId)->first();
        if ($exists) $this->col('coin_sellers')->where('real_id', $realId)->update($payload);
        else { $payload['created_at'] = now(); $this->col('coin_sellers')->insert($payload); }
        $this->col('users')->where('real_id', $realId)->orWhere('userId', $realId)->orWhere('appId', $realId)->update([
            'is_coin_seller' => true,
            'seller_type' => $data['seller_type'],
            'coin_seller_status' => 'active',
            'coin_seller_mobile' => $data['mobile'],
            'coin_seller_whatsapp' => $payload['whatsapp'],
            'updated_at' => now(),
        ]);
        $this->sendSystemMsg($realId, 'coin_seller_activated', 'Coin Seller Activated', 'Aapka Coin Seller Center active ho gaya hai.', ['seller_type'=>$data['seller_type']]);
        return back()->with('success', 'Coin Seller active ho gaya');
    }

    public function coinSellerCoins(Request $request, $realId)
    {
        $data = $request->validate(['action'=>'required|in:add,deduct','coins'=>'required|integer|min:1']);
        $seller = $this->col('coin_sellers')->where('real_id', $realId)->first();
        if (!$seller) return back()->with('error', 'Coin Seller nahi mila');
        if ($data['action'] === 'add') $this->col('coin_sellers')->where('real_id', $realId)->increment('coin_balance', (int)$data['coins']);
        else $this->col('coin_sellers')->where('real_id', $realId)->decrement('coin_balance', (int)$data['coins']);
        $this->col('coin_seller_transactions')->insert(['seller_real_id'=>$realId,'type'=>'admin_'.$data['action'],'coins'=>(int)$data['coins'],'created_at'=>now()]);
        return back()->with('success', 'Coin balance update ho gaya');
    }

    public function coinSellerStatus(Request $request, $realId)
    {
        $data = $request->validate(['status'=>'required|in:active,deactive,inactive']);
        $this->col('coin_sellers')->where('real_id', $realId)->update(['status'=>$data['status'],'updated_at'=>now()]);
        $this->col('users')->where('real_id', $realId)->orWhere('userId', $realId)->orWhere('appId', $realId)->update(['coin_seller_status'=>$data['status'],'updated_at'=>now()]);
        return back()->with('success', 'Coin Seller status update ho gaya');
    }

}
