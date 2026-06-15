<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use MongoDB\BSON\ObjectId;
use Throwable;
use App\Events\AppRealtimeEvent;
use App\Events\PrivateRealtimeEvent;

class AppController extends Controller
{
    private function col(string $name) { return DB::connection('mongodb')->table($name); }
    private function oid($id) { try { return new ObjectId($id); } catch (Throwable $e) { return $id; } }
    private function arr($item) { return json_decode(json_encode($item), true); }
    private function rows(string $collection, int $limit = 100) {
        return $this->col($collection)->orderBy('_id', 'desc')->limit($limit)->get()->map(fn($i) => $this->arr($i))->values();
    }
    private function uid(Request $request) {
        return $request->header('X-User-Id') ?: $request->input('uid') ?: $request->input('firebase_uid');
    }

    private function broadcastPublic(string $channel, string $event, array $payload = []): void
    {
        try { event(new AppRealtimeEvent($channel, $event, $payload)); } catch (Throwable $e) { report($e); }
    }

    private function broadcastUser(string $userId, string $event, array $payload = []): void
    {
        if (trim($userId) === '') return;
        try { event(new AppRealtimeEvent('user.'.$userId, $event, $payload)); } catch (Throwable $e) { report($e); }
    }

    /**
     * Send a production system message to exactly one user.
     * It is saved in MongoDB and broadcast on channel user.{user_id}.
     * Supported types: otp, host_verified, agency_join, agency_kick, gift_received,
     * follow, ban, warning, level_up, withdrawal, support_reply.
     */
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
        $this->broadcastUser($userId, 'system.message', ['system_message' => $message]);
        $this->broadcastUser($userId, 'notification.created', ['notification' => $message]);
        return $message;
    }

    private function broadcastRoom(string $roomId, string $event, array $payload = []): void
    {
        if (trim($roomId) === '') return;
        $this->broadcastPublic('room.'.$roomId, $event, $payload);
    }

    public function firebaseLogin(Request $request)
    {
        $data = $request->validate([
            'firebase_uid' => 'required|string',
            'name' => 'nullable|string',
            'email' => 'nullable|string',
            'phone' => 'nullable|string',
            'photo' => 'nullable|string',
            'country' => 'nullable|string',
            'gender' => 'nullable|string',
            'fcm_token' => 'nullable|string',
        ]);

        $user = $this->col('users')->where('firebase_uid', $data['firebase_uid'])->first();
        $payload = [
            'firebase_uid' => $data['firebase_uid'],
            'name' => $data['name'] ?? 'RRR User',
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? '',
            'image' => $data['photo'] ?? '',
            'country' => $data['country'] ?? 'India',
            'gender' => $data['gender'] ?? '',
            'fcm_token' => $data['fcm_token'] ?? '',
            'coins' => 100,
            'diamonds' => 0,
            'beans' => 0,
            'level' => 1,
            'vipLevel' => 0,
            'isVip' => false,
            'status' => 'active',
            'isOnline' => true,
            'updated_at' => now(),
        ];

        if ($user && isset($user->_id)) {
            unset($payload['coins'], $payload['diamonds'], $payload['beans'], $payload['level']);
            $this->col('users')->where('_id', $user->_id)->update($payload);
            $user = $this->col('users')->where('_id', $user->_id)->first();
        } else {
            $payload['userId'] = $this->generateRealId();
            $payload['real_id'] = $payload['userId'];
            $payload['appId'] = $payload['userId'];
            $payload['created_at'] = now();
            $id = $this->col('users')->insertGetId($payload);
            $this->col('wallet')->insert([
                'user_id' => (string) $id,
                'firebase_uid' => $data['firebase_uid'],
                'coins' => 100,
                'diamonds' => 0,
                'beans' => 0,
                'totalRecharge' => 0,
                'totalWithdraw' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $user = $this->col('users')->where('_id', $id)->first();
        }

        return response()->json(['success' => true, 'user' => $this->arr($user)]);
    }

    public function home()
    {
        return response()->json([
            'success' => true,
            'banners' => $this->rows('banners', 10),
            'rooms' => $this->col('rooms')->where('status', 'active')->orderBy('_id', 'desc')->limit(30)->get()->map(fn($i)=>$this->arr($i))->values(),
            'top_hosts' => $this->col('hosts')->where('status', 'active')->orderBy('diamonds', 'desc')->limit(20)->get()->map(fn($i)=>$this->arr($i))->values(),
            'families' => $this->rows('families', 10),
            'events' => $this->rows('events', 10),
        ]);
    }

    public function settings(){ return response()->json(['success'=>true,'settings'=>$this->arr($this->col('settings')->first() ?? [])]); }
    public function rooms(){ return response()->json(['success'=>true,'rooms'=>$this->rows('rooms', 100)]); }
    public function gifts(){ return response()->json(['success'=>true,'gifts'=>$this->col('gifts')->where('status','active')->orderBy('price','asc')->get()->map(fn($i)=>$this->arr($i))->values()]); }
    public function moments(){ return response()->json(['success'=>true,'moments'=>$this->rows('moments', 100)]); }
    public function families(){ return response()->json(['success'=>true,'families'=>$this->rows('families', 100)]); }
    public function vipPlans(){ return response()->json(['success'=>true,'plans'=>$this->rows('vip_plans', 100)]); }
    public function rechargePackages(){ return response()->json(['success'=>true,'packages'=>$this->rows('recharge_packages', 100)]); }
    public function banners(){ return response()->json(['success'=>true,'banners'=>$this->rows('banners', 50)]); }
    public function events(){ return response()->json(['success'=>true,'events'=>$this->rows('events', 50)]); }
    public function games(){ return response()->json(['success'=>true,'games'=>$this->rows('games', 50)]); }

    public function createRoom(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:120', 'theme' => 'nullable|string', 'maxSeats' => 'nullable|integer',
            'tags' => 'nullable|array', 'host_video_enabled' => 'nullable|boolean', 'video_rate_per_minute' => 'nullable|numeric'
        ]);
        $uid = $this->uid($request);
        $room = [
            'title' => $data['title'], 'creatorId' => $uid, 'creatorName' => $request->input('creatorName','Host'),
            'maxSeats' => $data['maxSeats'] ?? 12, 'currentUsers' => 1, 'theme' => $data['theme'] ?? 'party',
            'tags' => $data['tags'] ?? [], 'status' => 'active', 'channel_name' => 'room_'.uniqid(),
            'host_video_enabled' => (bool)($data['host_video_enabled'] ?? false),
            'video_rate_per_minute' => (int)($data['video_rate_per_minute'] ?? 0),
            'created_at' => now(), 'updated_at' => now(),
        ];
        $id = $this->col('rooms')->insertGetId($room);
        $room['_id'] = (string)$id;
        $this->broadcastPublic('rrr.public', 'room.created', ['room'=>$room]);
        return response()->json(['success'=>true,'room'=>$room]);
    }

    public function joinRoom(Request $request, $id){ $uid=$this->uid($request); $this->col('room_members')->updateOrInsert(['room_id'=>$id,'user_id'=>$uid], ['joined_at'=>now(),'status'=>'online']); $this->col('rooms')->where('_id',$this->oid($id))->increment('currentUsers', 1); $room=$this->col('rooms')->where('_id',$this->oid($id))->first(); $this->broadcastRoom((string)$id, 'room.user.joined', ['room_id'=>(string)$id,'user_id'=>$uid,'currentUsers'=>(int)($room->currentUsers ?? 0)]); return response()->json(['success'=>true]); }
    public function leaveRoom(Request $request, $id){ $uid=$this->uid($request); $this->col('room_members')->where('room_id',$id)->where('user_id',$uid)->update(['status'=>'left','left_at'=>now()]); $this->col('rooms')->where('_id',$this->oid($id))->decrement('currentUsers', 1); $room=$this->col('rooms')->where('_id',$this->oid($id))->first(); $this->broadcastRoom((string)$id, 'room.user.left', ['room_id'=>(string)$id,'user_id'=>$uid,'currentUsers'=>max(0,(int)($room->currentUsers ?? 0))]); return response()->json(['success'=>true]); }

    public function wallet(Request $request)
    {
        $uid = $this->uid($request);
        $wallet = $this->col('wallet')->where('firebase_uid', $uid)->orWhere('user_id', $uid)->first();
        return response()->json(['success'=>true,'wallet'=>$this->arr($wallet ?? ['coins'=>0,'diamonds'=>0,'beans'=>0])]);
    }

    public function profile(Request $request, $id = null)
    {
        $key = $id ?: $this->uid($request);
        $user = $this->findUser($key);
        return response()->json(['success'=>(bool)$user,'user'=>$this->publicUserPayload($user)]);
    }

    public function updateProfile(Request $request)
    {
        $uid = $this->uid($request);
        $user = $this->findUser($uid);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $data = $request->validate([
            'name' => 'nullable|string|max:80',
            'country' => 'nullable|string|max:80',
            'gender' => 'nullable|string|max:30',
            'bio' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|string',
            'voice_bio' => 'nullable|string|max:500',
            'language' => 'nullable|string|max:40',
            'dp' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'profile_photos' => 'nullable|array|max:5',
            'profile_photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $payload = collect($data)->except(['dp', 'profile_photos'])->toArray();
        if (isset($payload['description'])) {
            $payload['bio'] = $payload['description'];
        }

        if ($request->hasFile('dp')) {
            $path = $request->file('dp')->store('profiles/dp', 'public');
            $payload['image'] = url('storage/'.$path);
            $payload['dp'] = $payload['image'];
            $payload['avatar'] = $payload['image'];
            $payload['profileImage'] = $payload['image'];
        }

        $photos = [];
        if ($request->hasFile('profile_photos')) {
            foreach (array_slice($request->file('profile_photos'), 0, 5) as $file) {
                $path = $file->store('profiles/gallery', 'public');
                $photos[] = url('storage/'.$path);
            }
            $payload['profile_photos'] = $photos;
            $payload['profilePhotos'] = $photos;
            $payload['gallery'] = $photos;
        }

        $payload['updated_at'] = now();
        $this->col('users')->where('_id', $user->_id)->update($payload);
        $updated = $this->col('users')->where('_id', $user->_id)->first();

        $this->broadcastUser((string)($user->firebase_uid ?? $uid), 'profile.updated', ['user' => $this->arr($updated)]);

        return response()->json(['success'=>true, 'user' => $this->arr($updated)]);
    }

    public function createMoment(Request $request)
    {
        $data = $request->validate([
            'text'=>'nullable|string',
            'caption'=>'nullable|string',
            'media_url'=>'nullable|string',
            'type'=>'nullable|string',
            'media'=>'nullable|file|mimes:jpg,jpeg,png,webp,mp4,mov|max:20480',
        ]);
        if ($request->hasFile('media')) {
            $path = $request->file('media')->store('moments', 'public');
            $data['media_url'] = url('storage/'.$path);
        }
        $data['text'] = $data['text'] ?? ($data['caption'] ?? '');
        unset($data['caption'], $data['media']);
        $data += ['user_id'=>$this->uid($request),'likes'=>0,'comments'=>0,'status'=>'active','created_at'=>now(),'updated_at'=>now()];
        $id = $this->col('moments')->insertGetId($data);
        $data['_id'] = (string)$id;
        $this->broadcastPublic('rrr.public', 'moment.created', ['moment'=>$data]);
        return response()->json(['success'=>true,'id'=>(string)$id, 'moment'=>$data]);
    }

    public function sendGift(Request $request)
    {
        $data = $request->validate([
            'gift_id'=>'required|string',
            'receiver_id'=>'required|string',
            'room_id'=>'nullable|string',
            'price'=>'required|numeric',
            'pk_battle_id'=>'nullable|string',
            'pk_side'=>'nullable|in:a,b'
        ]);

        $sender = $this->uid($request);
        $price = (int) $data['price'];

        $wallet = $this->col('wallet')->where('firebase_uid', $sender)->orWhere('user_id', $sender)->first();
        if (!$wallet || (($wallet->coins ?? 0) < $price)) {
            return response()->json(['success'=>false,'message'=>'Insufficient coins'], 422);
        }

        $this->col('wallet')->where('firebase_uid', $sender)->decrement('coins', $price);
        $this->col('users')->where('firebase_uid', $sender)->decrement('coins', $price);
        $this->col('users')->where('firebase_uid', $data['receiver_id'])->increment('beans', $price);

        $data += ['sender_id'=>$sender,'created_at'=>now()];
        $this->col('gift_logs')->insert($data);
        if (!empty($data['room_id'])) { $this->broadcastRoom((string)$data['room_id'], 'gift.sent', ['gift'=>$data]); }
        $this->broadcastUser((string)$data['receiver_id'], 'gift.received', ['gift'=>$data]);

        if (!empty($data['pk_battle_id'])) {
            $field = ($data['pk_side'] ?? 'a') === 'b' ? 'score_b' : 'score_a';
            $this->col('pk_battles')->where('_id', $this->oid($data['pk_battle_id']))->increment($field, $price);
        }

        return response()->json(['success'=>true]);
    }

    public function rankings($type = 'rich')
    {
        $collection = $type === 'family' ? 'families' : ($type === 'room' ? 'rooms' : 'users');
        $field = $type === 'host' ? 'diamonds' : 'coins';
        return response()->json(['success'=>true,'type'=>$type,'items'=>$this->col($collection)->orderBy($field,'desc')->limit(100)->get()->map(fn($i)=>$this->arr($i))->values()]);
    }

    public function requestVideoCall(Request $request)
    {
        $data = $request->validate(['host_id'=>'required|string','room_id'=>'nullable|string','rate_per_minute'=>'nullable|numeric']);
        $data += ['caller_id'=>$this->uid($request),'status'=>'pending','channel_name'=>'video_'.uniqid(),'created_at'=>now(),'updated_at'=>now()];
        $id = $this->col('video_calls')->insertGetId($data);
        return response()->json(['success'=>true,'call_id'=>(string)$id,'call'=>$data]);
    }

    public function respondVideoCall(Request $request)
    {
        $data = $request->validate(['call_id'=>'required|string','status'=>'required|in:accepted,rejected']);
        $this->col('video_calls')->where('_id',$this->oid($data['call_id']))->update(['status'=>$data['status'],'responded_at'=>now(),'updated_at'=>now()]);
        return response()->json(['success'=>true]);
    }

    public function endVideoCall(Request $request)
    {
        $data = $request->validate(['call_id'=>'required|string','duration_seconds'=>'nullable|integer']);
        $this->col('video_calls')->where('_id',$this->oid($data['call_id']))->update(['status'=>'ended','duration_seconds'=>$data['duration_seconds'] ?? 0,'ended_at'=>now(),'updated_at'=>now()]);
        return response()->json(['success'=>true]);
    }

    // V2 family, VIP, PK, events
    public function familyDetail($id){ return response()->json(['success'=>true,'family'=>$this->arr($this->col('families')->where('_id',$this->oid($id))->first() ?? []),'members'=>$this->col('family_members')->where('family_id',$id)->get()->map(fn($i)=>$this->arr($i))->values(),'chat'=>$this->col('family_chat')->where('family_id',$id)->orderBy('_id','desc')->limit(50)->get()->map(fn($i)=>$this->arr($i))->values()]); }
    public function createFamily(Request $request){ $data=$request->validate(['name'=>'required|string','description'=>'nullable|string','country'=>'nullable|string','avatar'=>'nullable|string']); $data += ['owner_id'=>$this->uid($request),'level'=>1,'members_count'=>1,'coins'=>0,'status'=>'active','created_at'=>now(),'updated_at'=>now()]; $id=$this->col('families')->insertGetId($data); $this->col('family_members')->insert(['family_id'=>(string)$id,'user_id'=>$this->uid($request),'role'=>'King','joined_at'=>now()]); return response()->json(['success'=>true,'id'=>(string)$id]); }
    public function joinFamily(Request $request,$id){ $this->col('family_members')->updateOrInsert(['family_id'=>$id,'user_id'=>$this->uid($request)],['role'=>'Member','status'=>'active','joined_at'=>now()]); $this->col('families')->where('_id',$this->oid($id))->increment('members_count',1); return response()->json(['success'=>true]); }
    public function sendFamilyChat(Request $request){ $data=$request->validate(['family_id'=>'required|string','message'=>'required|string']); $data += ['user_id'=>$this->uid($request),'created_at'=>now()]; $this->col('family_chat')->insert($data); return response()->json(['success'=>true]); }
    public function buyVip(Request $request){ $data=$request->validate(['plan_id'=>'required|string','days'=>'nullable|integer','price'=>'nullable|numeric']); $uid=$this->uid($request); $price=(int)($data['price']??0); $wallet=$this->col('wallet')->where('firebase_uid',$uid)->orWhere('user_id',$uid)->first(); if($price>0 && (!$wallet || (($wallet->coins??0)<$price))){ return response()->json(['success'=>false,'message'=>'Insufficient coins'],422); } if($price>0){ $this->col('wallet')->where('firebase_uid',$uid)->orWhere('user_id',$uid)->decrement('coins',$price); } $data += ['user_id'=>$uid,'status'=>'active','started_at'=>now(),'expires_at'=>now()->addDays($request->input('days',30)),'created_at'=>now()]; $this->col('user_vips')->insert($data); $this->col('users')->where('firebase_uid',$uid)->update(['isVip'=>true,'vipLevel'=>$request->input('vip_level',1),'updated_at'=>now()]); return response()->json(['success'=>true]); }
    public function startPkBattle(Request $request){ $data=$request->validate(['host_a'=>'required|string','host_b'=>'required|string','type'=>'nullable|string','duration'=>'nullable|integer']); $data += ['score_a'=>0,'score_b'=>0,'status'=>'live','started_at'=>now(),'created_at'=>now()]; $id=$this->col('pk_battles')->insertGetId($data); return response()->json(['success'=>true,'battle_id'=>(string)$id]); }
    public function updatePkScore(Request $request){ $data=$request->validate(['battle_id'=>'required|string','side'=>'required|string','points'=>'required|integer']); $field=$data['side']==='b'?'score_b':'score_a'; $this->col('pk_battles')->where('_id',$this->oid($data['battle_id']))->increment($field,$data['points']); return response()->json(['success'=>true]); }
    public function endPkBattle(Request $request){ $id=$request->input('battle_id'); $battle=$this->col('pk_battles')->where('_id',$this->oid($id))->first(); $winner='draw'; if($battle){ $winner=($battle->score_a??0)>($battle->score_b??0)?'a':((($battle->score_b??0)>($battle->score_a??0))?'b':'draw'); } $this->col('pk_battles')->where('_id',$this->oid($id))->update(['status'=>'ended','winner'=>$winner,'ended_at'=>now()]); return response()->json(['success'=>true,'winner'=>$winner]); }
    public function joinEvent(Request $request,$id){ $this->col('event_participants')->updateOrInsert(['event_id'=>$id,'user_id'=>$this->uid($request)],['joined_at'=>now(),'status'=>'joined']); return response()->json(['success'=>true]); }
    public function appNotifications(Request $request)
    {
        $uid = $this->uid($request);
        $normal = $this->col('notifications')->where('user_id',$uid)->orWhere('user_id','all')->orderBy('_id','desc')->limit(100)->get()->map(fn($i)=>$this->arr($i))->values()->all();
        $system = $this->col('system_messages')->where('user_id',$uid)->orderBy('_id','desc')->limit(100)->get()->map(fn($i)=>$this->arr($i))->values()->all();
        $items = array_merge($normal, $system);
        usort($items, fn($a,$b) => strcmp((string)($b['created_at'] ?? ''), (string)($a['created_at'] ?? '')));
        return response()->json(['success'=>true,'notifications'=>array_slice($items,0,100)]);
    }
    public function readNotifications(Request $request)
    {
        $uid = $this->uid($request);
        $this->col('notifications_read')->updateOrInsert(['user_id'=>$uid],['read_at'=>now()]);
        $this->col('system_messages')->where('user_id',$uid)->update(['is_read'=>true,'updated_at'=>now()]);
        return response()->json(['success'=>true]);
    }

    public function systemMessages(Request $request)
    {
        $uid = $this->uid($request);
        return response()->json([
            'success'=>true,
            'messages'=>$this->col('system_messages')->where('user_id',$uid)->orderBy('_id','desc')->limit(100)->get()->map(fn($i)=>$this->arr($i))->values()
        ]);
    }

    public function readSystemMessages(Request $request)
    {
        $uid = $this->uid($request);
        $this->col('system_messages')->where('user_id',$uid)->update(['is_read'=>true,'updated_at'=>now()]);
        return response()->json(['success'=>true]);
    }

    public function adminSendSystemMessage(Request $request)
    {
        $data = $request->validate([
            'user_id'=>'required|string',
            'type'=>'required|string',
            'title'=>'required|string|max:120',
            'body'=>'required|string|max:2000',
            'data'=>'nullable|array',
        ]);
        $message = $this->sendSystemMsg($data['user_id'], $data['type'], $data['title'], $data['body'], $data['data'] ?? []);
        return response()->json(['success'=>true, 'message'=>$message]);
    }

    // V3 social/audio content
    public function voiceReels(){ return response()->json(['success'=>true,'reels'=>$this->rows('voice_reels',100)]); }
    public function createVoiceReel(Request $request){ $data=$request->validate(['audio_url'=>'required|string','caption'=>'nullable|string','duration'=>'nullable|integer']); $data += ['user_id'=>$this->uid($request),'likes'=>0,'shares'=>0,'status'=>'active','created_at'=>now()]; $id=$this->col('voice_reels')->insertGetId($data); return response()->json(['success'=>true,'id'=>(string)$id]); }
    public function stories(){ return response()->json(['success'=>true,'stories'=>$this->col('stories')->where('expires_at','>',now())->orderBy('_id','desc')->limit(100)->get()->map(fn($i)=>$this->arr($i))->values()]); }
    public function createStory(Request $request){ $data=$request->validate(['media_url'=>'required|string','type'=>'nullable|string','text'=>'nullable|string']); $data += ['user_id'=>$this->uid($request),'created_at'=>now(),'expires_at'=>now()->addDay()]; $id=$this->col('stories')->insertGetId($data); return response()->json(['success'=>true,'id'=>(string)$id]); }
    public function podcasts(){ return response()->json(['success'=>true,'podcasts'=>$this->rows('podcasts',100)]); }
    public function createPodcast(Request $request){ $data=$request->validate(['title'=>'required|string','audio_url'=>'required|string','description'=>'nullable|string']); $data += ['host_id'=>$this->uid($request),'plays'=>0,'status'=>'active','created_at'=>now()]; $id=$this->col('podcasts')->insertGetId($data); return response()->json(['success'=>true,'id'=>(string)$id]); }
    public function countryWar(){ return response()->json(['success'=>true,'countries'=>$this->rows('country_wars',100)]); }
    public function aiHostMessage(Request $request){ $message=$request->input('message',''); return response()->json(['success'=>true,'reply'=>'AI Host: Welcome! Aapka message mila - '.$message]); }

    // V4/V5 economy, creator, missions
    public function marketplace(){ return response()->json(['success'=>true,'items'=>$this->rows('marketplace_items',100)]); }
    public function buyMarketplaceItem(Request $request){ $data=$request->validate(['item_id'=>'required|string','price'=>'nullable|numeric']); $uid=$this->uid($request); $price=(int)($data['price']??0); $wallet=$this->col('wallet')->where('firebase_uid',$uid)->orWhere('user_id',$uid)->first(); if($price>0 && (!$wallet || (($wallet->coins??0)<$price))){ return response()->json(['success'=>false,'message'=>'Insufficient coins'],422); } if($price>0){ $this->col('wallet')->where('firebase_uid',$uid)->orWhere('user_id',$uid)->decrement('coins',$price); } $data += ['buyer_id'=>$uid,'status'=>'purchased','created_at'=>now()]; $this->col('marketplace_orders')->insert($data); $this->col('user_inventory')->insert(['user_id'=>$uid,'item_id'=>$data['item_id'],'source'=>'marketplace','created_at'=>now()]); return response()->json(['success'=>true]); }
    public function creatorShop($host_id=null){ $q=$this->col('creator_shop_items'); if($host_id){ $q=$q->where('host_id',$host_id); } return response()->json(['success'=>true,'items'=>$q->orderBy('_id','desc')->limit(100)->get()->map(fn($i)=>$this->arr($i))->values()]); }
    public function creatorSubscribe(Request $request){ $data=$request->validate(['host_id'=>'required|string','plan'=>'nullable|string','price'=>'nullable|numeric']); $data += ['user_id'=>$this->uid($request),'status'=>'active','created_at'=>now(),'expires_at'=>now()->addMonth()]; $this->col('creator_subscriptions')->insert($data); return response()->json(['success'=>true]); }
    public function dailyCheckin(Request $request){ $uid=$this->uid($request); $today=now()->format('Y-m-d'); $exists=$this->col('daily_checkins')->where('user_id',$uid)->where('date',$today)->first(); if($exists){ return response()->json(['success'=>true,'already'=>true,'coins'=>0]); } $this->col('daily_checkins')->insert(['user_id'=>$uid,'date'=>$today,'coins'=>50,'created_at'=>now()]); $this->col('users')->where('firebase_uid',$uid)->increment('coins',50); return response()->json(['success'=>true,'coins'=>50]); }
    public function completeMission(Request $request){ $data=$request->validate(['mission_id'=>'required|string','reward'=>'nullable|integer']); $data += ['user_id'=>$this->uid($request),'completed_at'=>now()]; $this->col('mission_completions')->insert($data); return response()->json(['success'=>true,'reward'=>$request->input('reward',0)]); }


    // ------------------------------------------------------------------
    // FREE / LOW-COST AI MODULES
    // These are production-safe placeholders that do not require paid AI APIs.
    // Later you can replace the internals with OpenAI/Google/Azure providers.
    // ------------------------------------------------------------------
    private function aiSettings(): array
    {
        $settings = $this->appSettings();
        return [
            'moderator_enabled' => (bool)($settings['ai_moderator_enabled'] ?? true),
            'translation_enabled' => (bool)($settings['ai_subtitle_translation_enabled'] ?? true),
            'matchmaking_enabled' => (bool)($settings['ai_matchmaking_enabled'] ?? true),
            'event_host_enabled' => (bool)($settings['ai_event_host_enabled'] ?? true),
            'recommendations_enabled' => (bool)($settings['ai_recommendations_enabled'] ?? true),
            'default_language' => $settings['ai_default_language'] ?? 'hi',
        ];
    }

    public function aiModeratorSettings()
    {
        return response()->json(['success' => true, 'settings' => $this->aiSettings()]);
    }

    public function aiModeratorCheck(Request $request)
    {
        $data = $request->validate([
            'text' => 'nullable|string',
            'room_id' => 'nullable|string',
            'content_type' => 'nullable|string',
        ]);
        $text = mb_strtolower($data['text'] ?? '');
        $blocked = [
            'abuse', 'spam', 'scam', 'fraud', 'nude', 'adult', 'hate', 'kill',
            'gaali', 'gali', 'chutiya', 'madarchod', 'bhosdi', 'randi'
        ];
        $hits = [];
        foreach ($blocked as $word) {
            if ($word !== '' && str_contains($text, $word)) $hits[] = $word;
        }
        $severity = count($hits) >= 2 ? 'high' : (count($hits) === 1 ? 'medium' : 'safe');
        $action = $severity === 'high' ? 'mute' : ($severity === 'medium' ? 'warn' : 'allow');
        $log = [
            'user_id' => $this->uid($request),
            'room_id' => $data['room_id'] ?? '',
            'content_type' => $data['content_type'] ?? 'text',
            'text' => $data['text'] ?? '',
            'hits' => $hits,
            'severity' => $severity,
            'action' => $action,
            'created_at' => now(),
        ];
        $this->col('ai_moderation_logs')->insert($log);
        return response()->json(['success' => true, 'allowed' => $action === 'allow', 'severity' => $severity, 'action' => $action, 'hits' => $hits]);
    }

    public function aiSubtitleTranslate(Request $request)
    {
        $data = $request->validate([
            'text' => 'required|string',
            'from' => 'nullable|string',
            'to' => 'nullable|string',
        ]);
        $to = $data['to'] ?? 'en';
        $dictionary = [
            'hi:en' => ['namaste'=>'hello','kaise ho'=>'how are you','shukriya'=>'thank you','dhanyawad'=>'thank you','swagat'=>'welcome','gift bhejo'=>'send gift'],
            'en:hi' => ['hello'=>'namaste','how are you'=>'kaise ho','thank you'=>'dhanyawad','welcome'=>'swagat','send gift'=>'gift bhejo'],
            'hi:ar' => ['namaste'=>'marhaba','shukriya'=>'shukran'],
            'ar:hi' => ['marhaba'=>'namaste','shukran'=>'shukriya'],
        ];
        $key = ($data['from'] ?? 'hi').':'.$to;
        $translated = mb_strtolower($data['text']);
        foreach (($dictionary[$key] ?? []) as $source => $target) {
            $translated = str_replace($source, $target, $translated);
        }
        $isFallback = $translated === mb_strtolower($data['text']);
        if ($isFallback) {
            $translated = '['.strtoupper($to).' subtitle] '.$data['text'];
        }
        $this->col('ai_translation_logs')->insert([
            'user_id' => $this->uid($request),
            'from' => $data['from'] ?? 'auto',
            'to' => $to,
            'source_text' => $data['text'],
            'translated_text' => $translated,
            'provider' => 'free_dictionary_fallback',
            'created_at' => now(),
        ]);
        return response()->json(['success' => true, 'translated_text' => $translated, 'provider' => 'free_dictionary_fallback', 'voice_to_voice' => false]);
    }

    public function aiMatchmaking(Request $request)
    {
        $uid = $this->uid($request);
        $current = $this->col('users')->where('firebase_uid', $uid)->orWhere('userId', $uid)->first();
        $country = $current->country ?? $request->input('country', 'India');
        $language = $request->input('language', 'Hindi');
        $users = $this->col('users')->where('status', 'active')->limit(50)->get()->map(function($u) use ($uid, $country, $language) {
            $a = $this->arr($u);
            $score = 50;
            if (($a['country'] ?? '') === $country) $score += 20;
            if (($a['language'] ?? '') === $language) $score += 10;
            $score += min(20, (int)($a['level'] ?? 1));
            $a['match_score'] = min(99, $score);
            return $a;
        })->filter(fn($u) => ($u['firebase_uid'] ?? $u['userId'] ?? '') !== $uid)->sortByDesc('match_score')->values()->take(20);
        $this->col('ai_matchmaking_logs')->insert(['user_id'=>$uid,'count'=>$users->count(),'created_at'=>now()]);
        return response()->json(['success' => true, 'matches' => $users]);
    }

    public function aiEventHostRun(Request $request)
    {
        $data = $request->validate([
            'event_id' => 'nullable|string',
            'room_id' => 'nullable|string',
            'type' => 'nullable|string',
            'language' => 'nullable|string',
        ]);
        $type = $data['type'] ?? 'welcome';
        $scripts = [
            'welcome' => 'Namaste dosto! RRR Voice Chat event me aapka swagat hai. Rules follow karein aur enjoy karein.',
            'quiz' => 'Quiz round start ho raha hai. Sabhi users ready ho jaiye. Fastest correct answer winner hoga.',
            'gift' => 'Wah! Room me gift aaya hai. Sender ko special thanks!',
            'winner' => 'Congratulations! Winner announce ho gaya. Sabhi participants ka shukriya.',
        ];
        $message = $scripts[$type] ?? $scripts['welcome'];
        $log = $data + ['message'=>$message,'provider'=>'scripted_free_ai_host','created_at'=>now()];
        $this->col('ai_event_host_logs')->insert($log);
        return response()->json(['success' => true, 'message' => $message, 'provider' => 'scripted_free_ai_host']);
    }

    public function aiRecommendations(Request $request)
    {
        $uid = $this->uid($request);
        $rooms = $this->col('rooms')->where('status','active')->orderBy('onlineUsers','desc')->limit(10)->get()->map(fn($i)=>$this->arr($i))->values();
        $hosts = $this->col('hosts')->where('status','active')->orderBy('diamonds','desc')->limit(10)->get()->map(fn($i)=>$this->arr($i))->values();
        $events = $this->rows('events', 10);
        $reels = $this->rows('voice_reels', 10);
        $this->col('ai_recommendation_logs')->insert(['user_id'=>$uid,'created_at'=>now(),'source'=>'free_ranked_rules']);
        return response()->json([
            'success' => true,
            'provider' => 'free_ranked_rules',
            'rooms' => $rooms,
            'hosts' => $hosts,
            'events' => $events,
            'voice_reels' => $reels,
        ]);
    }




    // ------------------------------------------------------------------
    // PRODUCTION AUTH / PROFILE / NOTIFICATION HELPERS
    // ------------------------------------------------------------------
    private function generateRealId(): string
    {
        // Public Real ID rule: start at 555555 and increase by 1 forever.
        // MongoDB ObjectId/Firebase UID stay hidden; Real ID is shown/searchable everywhere.
        $counter = $this->col('counters')->where('key', 'real_id')->first();
        if (!$counter) {
            $this->col('counters')->insert(['key' => 'real_id', 'value' => 555554, 'created_at' => now(), 'updated_at' => now()]);
        }
        $this->col('counters')->where('key', 'real_id')->increment('value', 1);
        $counter = $this->col('counters')->where('key', 'real_id')->first();
        $id = max(555555, (int)($counter->value ?? 555555));
        while ($this->col('users')->where('userId', (string)$id)->orWhere('real_id', (string)$id)->orWhere('appId', (string)$id)->first()) {
            $this->col('counters')->where('key', 'real_id')->increment('value', 1);
            $counter = $this->col('counters')->where('key', 'real_id')->first();
            $id = (int)($counter->value ?? ($id + 1));
        }
        return (string)$id;
    }

    private function realIdOf($user): string
    {
        if (!$user) return '';
        return (string)(($user->real_id ?? null) ?: ($user->userId ?? null) ?: ($user->appId ?? ''));
    }

    private function roleBadgesFor($user): array
    {
        $badges = [];
        if (!$user) return $badges;
        $vip = (int)($user->vipLevel ?? $user->vip_level ?? 0);
        if ($vip > 0) $badges[] = ['type' => 'vip', 'label' => 'VIP '.$vip, 'asset' => 'assets/badges/vip_'.min(max($vip, 1), 6).'_badge.svg'];
        if (($user->is_verified ?? false) || (($user->verification_status ?? '') === 'verified')) $badges[] = ['type' => 'verified', 'label' => 'Verified', 'asset' => 'assets/badges/verified_badge.svg'];
        foreach (['host'=>'Host','agent'=>'Agent','bd'=>'BD','manager'=>'Manager','assistant'=>'Assistant','superadmin'=>'SuperAdmin'] as $key => $label) {
            $flag = 'is_'.$key;
            if (($user->{$flag} ?? false) || strtolower((string)($user->role ?? '')) === $key) $badges[] = ['type'=>$key, 'label'=>$label, 'asset'=>'assets/badges/'.$key.'_badge.svg'];
        }
        if (($user->is_coin_seller ?? false) || !empty($user->seller_type)) {
            $type = strtolower((string)($user->seller_type ?? 'normal'));
            if (!in_array($type, ['normal','medium','super'])) $type = 'normal';
            $badges[] = ['type'=>'coin_seller', 'label'=>ucfirst($type).' Coin Seller', 'asset'=>'assets/badges/'.$type.'_coin_seller_badge.svg'];
        }
        return $badges;
    }

    private function publicUserPayload($user): array
    {
        $arr = $this->arr($user ?? []);
        if (!$arr) return [];
        $realId = (string)(($arr['real_id'] ?? null) ?: ($arr['userId'] ?? null) ?: ($arr['appId'] ?? ''));
        unset($arr['_id'], $arr['firebase_uid'], $arr['uid'], $arr['password'], $arr['login_password']);
        $arr['real_id'] = $realId;
        $arr['userId'] = $realId;
        $arr['appId'] = $realId;
        $arr['role_badges'] = $this->roleBadgesFor((object)$arr);
        return $arr;
    }

    private function findUser($key)
    {
        if (!$key) return null;
        return $this->col('users')
            ->where('firebase_uid', $key)
            ->orWhere('userId', (string)$key)
            ->orWhere('real_id', (string)$key)
            ->orWhere('appId', (string)$key)
            ->orWhere('_id', $this->oid($key))
            ->first();
    }

    public function completeProfile(Request $request)
    {
        $uid = $this->uid($request);
        $data = $request->validate([
            'name' => 'required|string|max:80',
            'gender' => 'nullable|string|max:30',
            'country' => 'nullable|string|max:80',
            'invite_id' => 'nullable|string|max:30',
            'image' => 'nullable|string',
            'photo' => 'nullable|string',
            'password' => 'nullable|string|min:6|max:80',
            'login_password' => 'nullable|string|min:6|max:80',
        ]);
        $user = $this->findUser($uid);
        $realId = $user ? (($user->userId ?? null) ?: ($user->real_id ?? null) ?: ($user->appId ?? null)) : null;
        if (!$realId) $realId = $this->generateRealId();
        $payload = [
            'name' => $data['name'],
            'gender' => $data['gender'] ?? '',
            'country' => $data['country'] ?? 'India',
            'invite_id' => $data['invite_id'] ?? '',
            'image' => $data['image'] ?? ($data['photo'] ?? ($user->image ?? '')),
            'userId' => $realId,
            'real_id' => $realId,
            'appId' => $realId,
            'profile_completed' => true,
            'updated_at' => now(),
        ];
        $plainPassword = $data['password'] ?? ($data['login_password'] ?? null);
        if (!empty($plainPassword)) {
            $payload['login_password'] = password_hash($plainPassword, PASSWORD_BCRYPT);
        }
        if ($user && isset($user->_id)) {
            $this->col('users')->where('_id', $user->_id)->update($payload);
            $user = $this->col('users')->where('_id', $user->_id)->first();
        } else {
            $payload += ['firebase_uid' => $uid, 'coins' => 100, 'level' => 1, 'vipLevel' => 0, 'status' => 'active', 'created_at' => now()];
            $id = $this->col('users')->insertGetId($payload);
            $user = $this->col('users')->where('_id', $id)->first();
        }
        $arr = $this->arr($user);
        return response()->json(['success'=>true, 'user'=>$arr, 'real_id'=>$arr['real_id'] ?? ($arr['userId'] ?? ''), 'appId'=>$arr['appId'] ?? ($arr['userId'] ?? '')]);
    }

    public function authMe(Request $request)
    {
        $user = $this->findUser($this->uid($request));
        return response()->json(['success'=>(bool)$user, 'user'=>$this->arr($user ?? [])]);
    }

    public function realIdLogin(Request $request)
    {
        $data = $request->validate(['real_id'=>'nullable|string', 'login_id'=>'nullable|string', 'password'=>'required|string']);
        $loginId = $data['real_id'] ?? $data['login_id'] ?? '';
        if (!$loginId) return response()->json(['success'=>false, 'message'=>'Real ID required'], 422);
        $user = $this->findUser($loginId);
        if (!$user || empty($user->login_password) || !password_verify($data['password'], $user->login_password)) {
            return response()->json(['success'=>false, 'message'=>'Invalid Real ID or password'], 401);
        }
        $arr = $this->arr($user);
        return response()->json(['success'=>true, 'user'=>$arr, 'real_id'=>$arr['real_id'] ?? ($arr['userId'] ?? ''), 'appId'=>$arr['appId'] ?? ($arr['userId'] ?? '')]);
    }

    public function updateFcmToken(Request $request)
    {
        $uid = $this->uid($request);
        $data = $request->validate(['fcm_token'=>'required|string']);
        $this->col('users')->where('firebase_uid', $uid)->orWhere('userId', $uid)->update(['fcm_token'=>$data['fcm_token'], 'updated_at'=>now()]);
        return response()->json(['success'=>true, 'message'=>'FCM token updated']);
    }

    // ------------------------------------------------------------------
    // PRODUCTION MESSAGES
    // ------------------------------------------------------------------
    public function conversations(Request $request)
    {
        $uid = $this->uid($request);
        $items = $this->col('conversations')
            ->where('participant_ids', 'all', [$uid])
            ->orderBy('updated_at', 'desc')
            ->limit(100)->get()->map(fn($i)=>$this->arr($i))->values();
        return response()->json(['success'=>true, 'conversations'=>$items]);
    }

    public function chatMessages(Request $request, $withUser)
    {
        $uid = $this->uid($request);
        $other = $this->findUser($withUser);
        if (!$other) return response()->json(['success'=>false, 'message'=>'User not found'], 404);
        $otherId = $other->firebase_uid ?? $other->userId ?? (string)$other->_id;
        $messages = $this->col('messages')
            ->where(function($q) use ($uid, $otherId) { $q->where('sender_id', $uid)->where('receiver_id', $otherId); })
            ->orWhere(function($q) use ($uid, $otherId) { $q->where('sender_id', $otherId)->where('receiver_id', $uid); })
            ->orderBy('_id', 'asc')->limit(200)->get()->map(fn($i)=>$this->arr($i))->values();
        return response()->json(['success'=>true, 'user'=>$this->arr($other), 'messages'=>$messages]);
    }

    public function sendMessage(Request $request)
    {
        $data = $request->validate(['to'=>'required|string', 'message'=>'required|string|max:2000', 'type'=>'nullable|string']);
        $sender = $this->uid($request);
        $receiver = $this->findUser($data['to']);
        if (!$receiver) return response()->json(['success'=>false, 'message'=>'Receiver not found'], 404);
        $receiverId = $receiver->firebase_uid ?? $receiver->userId ?? (string)$receiver->_id;
        $msg = ['sender_id'=>$sender, 'receiver_id'=>$receiverId, 'message'=>$data['message'], 'type'=>$data['type'] ?? 'text', 'is_read'=>false, 'created_at'=>now()];
        $id = $this->col('messages')->insertGetId($msg);
        $participants = [$sender, $receiverId]; sort($participants);
        $this->col('conversations')->updateOrInsert(
            ['conversation_key'=>implode('_', $participants)],
            ['participant_ids'=>$participants, 'last_message'=>$data['message'], 'last_message_at'=>now(), 'updated_at'=>now()]
        );
        $notification = ['user_id'=>$receiverId, 'title'=>'New Message', 'body'=>$data['message'], 'type'=>'message', 'data'=>['from'=>$sender], 'created_at'=>now()];
        $this->col('notifications')->insert($notification);
        $msg['_id'] = (string)$id;
        $this->broadcastUser((string)$receiverId, 'message.sent', ['message'=>$msg]);
        $this->broadcastUser((string)$receiverId, 'notification.created', ['notification'=>$notification]);
        $this->broadcastUser((string)$sender, 'message.sent', ['message'=>$msg]);
        return response()->json(['success'=>true, 'id'=>(string)$id]);
    }

    public function sendMessageTo(Request $request, $withUser)
    {
        $request->merge(['to' => $withUser] + $request->all());
        return $this->sendMessage($request);
    }

    // ------------------------------------------------------------------
    // PRODUCTION MOMENTS
    // ------------------------------------------------------------------
    public function likeMoment(Request $request, $id)
    {
        $uid = $this->uid($request);
        $exists = $this->col('moment_likes')->where('moment_id', $id)->where('user_id', $uid)->first();
        if ($exists) return response()->json(['success'=>true, 'liked'=>true]);
        $this->col('moment_likes')->insert(['moment_id'=>$id, 'user_id'=>$uid, 'created_at'=>now()]);
        $this->col('moments')->where('_id', $this->oid($id))->increment('likes', 1);
        return response()->json(['success'=>true, 'liked'=>true]);
    }

    public function commentMoment(Request $request, $id)
    {
        $data = $request->validate(['comment'=>'required|string|max:500']);
        $comment = ['moment_id'=>$id, 'user_id'=>$this->uid($request), 'comment'=>$data['comment'], 'created_at'=>now()];
        $cid = $this->col('moment_comments')->insertGetId($comment);
        $this->col('moments')->where('_id', $this->oid($id))->increment('comments', 1);
        return response()->json(['success'=>true, 'id'=>(string)$cid]);
    }

    // ------------------------------------------------------------------
    // PRODUCTION AGENCY / BD IN-APP OTP FLOWS
    // ------------------------------------------------------------------
    private function createOtpInvite(Request $request, string $collection, string $targetRole, string $targetRealIdField = 'real_id')
    {
        $owner = $this->uid($request);
        $targetRealId = $request->input($targetRealIdField) ?: $request->input('hostRealId') ?: $request->input('agentRealId') ?: $request->input('real_id') ?: $request->input('user_id');
        if (!$targetRealId) return response()->json(['success'=>false, 'message'=>'Real ID required'], 422);
        $target = $this->findUser($targetRealId);
        if (!$target) return response()->json(['success'=>false, 'message'=>'User not found'], 404);
        $targetId = $target->firebase_uid ?? $target->userId ?? (string)$target->_id;
        $otp = (string) random_int(100000, 999999);
        $inviteId = $this->col($collection)->insertGetId([
            'owner_id'=>$owner, 'target_id'=>$targetId, 'target_real_id'=>$targetRealId, 'target_role'=>$targetRole,
            'otp'=>$otp, 'status'=>'pending', 'expires_at'=>now()->addMinutes(10), 'created_at'=>now(), 'updated_at'=>now()
        ]);
        $this->sendSystemMsg(
            (string)$targetId,
            'otp',
            'Your OTP Code',
            'Your OTP is '.$otp.'. Share this only if you want to join the requested '.$targetRole.' flow.',
            ['invite_id'=>(string)$inviteId, 'role'=>$targetRole, 'expires_in_minutes'=>10]
        );
        return response()->json(['success'=>true, 'invite_id'=>(string)$inviteId, 'invite'=>['inviteId'=>(string)$inviteId, 'invite_id'=>(string)$inviteId, 'hostRealId'=>$targetRealId, 'agentRealId'=>$targetRealId], 'message'=>'OTP sent to app Messages/Inbox']);
    }

    private function verifyOtpInvite(Request $request, string $inviteCollection, string $relationCollection, string $ownerField, string $memberField, string $roleName)
    {
        $data = $request->validate(['invite_id'=>'nullable|string', 'inviteId'=>'nullable|string', 'otp'=>'required|string']);
        $inviteKey = $data['invite_id'] ?? $data['inviteId'] ?? null;
        if (!$inviteKey) return response()->json(['success'=>false, 'message'=>'Invite ID required'], 422);
        $invite = $this->col($inviteCollection)->where('_id', $this->oid($inviteKey))->first();
        if (!$invite || ($invite->status ?? '') !== 'pending') return response()->json(['success'=>false, 'message'=>'Invalid invite'], 422);
        if (($invite->otp ?? '') !== $data['otp']) return response()->json(['success'=>false, 'message'=>'Invalid OTP'], 422);
        $this->col($relationCollection)->updateOrInsert(
            [$ownerField=>$invite->owner_id, $memberField=>$invite->target_id],
            ['status'=>'active', 'role'=>$roleName, 'joined_at'=>now(), 'updated_at'=>now()]
        );
        $this->col($inviteCollection)->where('_id', $this->oid($inviteKey))->update(['status'=>'verified', 'verified_at'=>now(), 'updated_at'=>now()]);
        $relation = $this->col($relationCollection)->where($ownerField, $invite->owner_id)->where($memberField, $invite->target_id)->first();
        if ($roleName === 'agent') {
            $this->sendSystemMsg((string)$invite->target_id, 'agency_join', 'Welcome to BD Center', 'You have been added under a BD.', ['owner_id'=>$invite->owner_id]);
        } else {
            $this->sendSystemMsg((string)$invite->target_id, 'agency_join', 'Welcome to Agency', 'You have been added under an agency.', ['owner_id'=>$invite->owner_id]);
        }
        $key = $roleName === 'agent' ? 'agent' : 'host';
        return response()->json(['success'=>true, 'message'=>$roleName.' added successfully', $key=>$this->arr($relation ?? [])]);
    }

    public function agencyHosts(Request $request)
    {
        $uid = $this->uid($request);
        $hosts = $this->col('agency_hosts')->where('agency_id', $uid)->orderBy('_id','desc')->limit(200)->get()->map(fn($i)=>$this->arr($i))->values();
        return response()->json(['success'=>true, 'hosts'=>$hosts]);
    }
    public function agencyHostInviteRequest(Request $request){ return $this->createOtpInvite($request, 'agency_host_invites', 'host', 'host_real_id'); }
    public function agencyHostInviteVerify(Request $request){ return $this->verifyOtpInvite($request, 'agency_host_invites', 'agency_hosts', 'agency_id', 'host_id', 'host'); }

    public function bdAgents(Request $request)
    {
        $uid = $this->uid($request);
        $agents = $this->col('bd_agents')->where('bd_id', $uid)->orderBy('_id','desc')->limit(200)->get()->map(fn($i)=>$this->arr($i))->values();
        return response()->json(['success'=>true, 'agents'=>$agents]);
    }
    public function bdAgentInviteRequest(Request $request){ return $this->createOtpInvite($request, 'bd_agent_invites', 'agent', 'agent_real_id'); }
    public function bdAgentInviteVerify(Request $request){ return $this->verifyOtpInvite($request, 'bd_agent_invites', 'bd_agents', 'bd_id', 'agent_id', 'agent'); }



    // ------------------------------------------------------------------
    // USER BASIC KYC VERIFICATION / BLUE VERIFIED BADGE
    // ------------------------------------------------------------------
    public function userVerificationStatus(Request $request)
    {
        $uid = $this->uid($request);
        if (!$uid) return response()->json(['success'=>false, 'message'=>'User auth required'], 401);
        $verification = $this->col('user_verifications')->where('user_id', $uid)->orderBy('_id', 'desc')->first();
        $user = $this->findUser($uid);
        return response()->json([
            'success' => true,
            'verification' => $this->arr($verification ?? [
                'status' => ($user->user_verification_status ?? 'not_submitted'),
                'gender' => $user->gender ?? '',
                'country' => $user->country ?? '',
                'message' => 'User verification submit nahi hua.',
            ]),
        ]);
    }

    public function submitUserVerification(Request $request)
    {
        $uid = $this->uid($request);
        if (!$uid) return response()->json(['success'=>false, 'message'=>'User auth required'], 401);

        $data = $request->validate([
            'selfie' => 'required|file|mimes:jpg,jpeg,png,webp|max:8192',
            'gender' => 'required|string|max:30',
            'country' => 'required|string|max:80',
            'client_face_check' => 'nullable|string',
        ]);

        $user = $this->findUser($uid);
        $path = $request->file('selfie')->store('user_verifications', 'public');
        $selfieUrl = url('storage/'.$path);

        // Basic production KYC: one clear face from Flutter MLKit + required gender/country.
        // For stronger anti-spoof/deepfake/liveness, integrate AWS Rekognition Face Liveness,
        // FaceTec, Onfido, Sumsub, or similar provider here and set status from provider result.
        $status = 'pending_review';
        $message = 'Face detected by ML Kit. Admin review required before blue verified badge.';

        $verification = [
            'user_id' => $uid,
            'real_id' => $user->real_id ?? $user->userId ?? $user->appId ?? '',
            'selfie_url' => $selfieUrl,
            'gender' => $data['gender'],
            'country' => $data['country'],
            'client_face_check' => $data['client_face_check'] ?? '',
            'provider' => 'flutter_mlkit_basic',
            'status' => $status,
            'message' => $message,
            'submitted_at' => now(),
            'updated_at' => now(),
        ];
        if ($status === 'verified') $verification['verified_at'] = now();
        $this->col('user_verifications')->insert($verification);

        $update = [
            'gender' => $data['gender'],
            'country' => $data['country'],
            'user_verification_status' => $status,
            'verification_status' => $status,
            'is_verified' => false,
            'verified_badge' => '',
            'verification_selfie_url' => $selfieUrl,
            'updated_at' => now(),
        ];
        $this->col('users')->where('firebase_uid', $uid)->orWhere('real_id', $uid)->orWhere('userId', $uid)->update($update);

        $this->col('notifications')->insert([
            'user_id' => $uid,
            'title' => 'Verification Submitted',
            'body' => 'Aapki user verification request admin review ke liye submit ho gayi hai.',
            'type' => 'user_verification',
            'is_read' => false,
            'created_at' => now(),
        ]);

        return response()->json(['success'=>true, 'verification'=>$verification]);
    }

    public function reviewUserVerification(Request $request, $userId)
    {
        $data = $request->validate([
            'status' => 'required|string|in:verified,approved,rejected,pending_review',
            'reason' => 'nullable|string|max:300',
        ]);
        $status = $data['status'] === 'approved' ? 'verified' : $data['status'];
        $user = $this->findUser($userId);
        if (!$user) return response()->json(['success'=>false, 'message'=>'User not found'], 404);
        $uid = $user->firebase_uid ?? $userId;
        $update = [
            'status' => $status,
            'message' => $status === 'verified' ? 'Verified by admin.' : ($data['reason'] ?? 'Reviewed by admin.'),
            'reviewed_at' => now(),
            'updated_at' => now(),
        ];
        if ($status === 'verified') $update['verified_at'] = now();
        if ($status === 'rejected') $update['rejection_reason'] = $data['reason'] ?? 'Rejected by admin.';
        $this->col('user_verifications')->where('user_id', $uid)->orderBy('_id', 'desc')->limit(1)->update($update);
        $this->col('users')->where('firebase_uid', $uid)->update([
            'user_verification_status' => $status,
            'verification_status' => $status,
            'is_verified' => false,
            'verified_badge' => '',
            'updated_at' => now(),
        ]);
        return response()->json(['success'=>true, 'status'=>$status]);
    }


    // ------------------------------------------------------------------
    // HOST FACE VERIFICATION / AUTO APPROVAL
    // ------------------------------------------------------------------
    public function hostVerificationStatus(Request $request)
    {
        $uid = $this->uid($request);
        $verification = $this->col('host_verifications')->where('user_id', $uid)->orderBy('_id', 'desc')->first();
        $user = $this->findUser($uid);
        return response()->json([
            'success' => true,
            'verification' => $this->arr($verification ?? [
                'status' => ($user->host_verification_status ?? 'not_submitted'),
                'message' => 'Host face verification submit nahi hua.',
            ]),
        ]);
    }

    public function submitHostVerification(Request $request)
    {
        $uid = $this->uid($request);
        if (!$uid) return response()->json(['success'=>false, 'message'=>'User auth required'], 401);

        $data = $request->validate([
            'selfie' => 'required|file|mimes:jpg,jpeg,png,webp|max:8192',
            'client_face_check' => 'nullable|string',
        ]);

        $path = $request->file('selfie')->store('host_verifications', 'public');
        $selfieUrl = url('storage/'.$path);

        // Flutter MLKit face detector sends client_face_check=passed only after one clear face is detected.
        // Launch mode is FREE ML Kit + MANUAL ADMIN REVIEW. No automatic host approval here.
        // Future upgrade: connect AWS Rekognition Face Liveness / FaceTec and auto-approve by score.
        $status = 'pending_review';
        $message = 'Face detected by ML Kit. Host verification requires admin review.';

        $verification = [
            'user_id' => $uid,
            'selfie_url' => $selfieUrl,
            'client_face_check' => $data['client_face_check'] ?? '',
            'provider' => 'flutter_mlkit_basic_face_check',
            'status' => $status,
            'message' => $message,
            'submitted_at' => now(),
            'updated_at' => now(),
        ];
        $id = $this->col('host_verifications')->insertGetId($verification);

        $this->col('users')->where('firebase_uid', $uid)->orWhere('userId', $uid)->orWhere('real_id', $uid)->update([
            'host_verification_status' => $status,
            'host_selfie_url' => $selfieUrl,
            'host_verified_at' => null,
            'updated_at' => now(),
        ]);

        $this->col('hosts')->updateOrInsert(
            ['user_id' => $uid],
            [
                'user_id' => $uid,
                'status' => 'pending_review',
                'verification_status' => $status,
                'selfie_url' => $selfieUrl,
                'approved_at' => null,
                'updated_at' => now(),
            ]
        );

        $verification['_id'] = (string) $id;
        return response()->json(['success'=>true, 'verification'=>$verification]);
    }

    public function reviewHostVerification(Request $request, $userId)
    {
        $data = $request->validate([
            'status' => 'required|in:approved,rejected,pending_review',
            'message' => 'nullable|string|max:500',
        ]);
        $status = $data['status'];
        $this->col('host_verifications')->where('user_id', $userId)->orderBy('_id', 'desc')->limit(1)->update([
            'status' => $status,
            'message' => $data['message'] ?? ($status === 'approved' ? 'Approved by admin.' : 'Updated by admin.'),
            'reviewed_at' => now(),
            'updated_at' => now(),
        ]);
        $this->col('users')->where('firebase_uid', $userId)->orWhere('userId', $userId)->orWhere('real_id', $userId)->update([
            'role' => $status === 'approved' ? 'host' : 'user',
            'isHost' => $status === 'approved',
            'host_verification_status' => $status,
            'host_verified_at' => $status === 'approved' ? now() : null,
            'updated_at' => now(),
        ]);
        $this->col('hosts')->updateOrInsert(['user_id'=>$userId], [
            'user_id'=>$userId,
            'status'=>$status === 'approved' ? 'active' : $status,
            'verification_status'=>$status,
            'approved_at'=>$status === 'approved' ? now() : null,
            'updated_at'=>now(),
        ]);
        return response()->json(['success'=>true, 'message'=>'Host verification updated']);
    }


    private function findOrCreateHostSevenDayTask(string $uid): array
    {
        $user = $this->findUser($uid);
        if (!$user) {
            return ['error' => 'User not found'];
        }
        $userArr = $this->arr($user);
        $isHost = ($userArr['isHost'] ?? false) || (($userArr['role'] ?? '') === 'host') || (($userArr['host_verification_status'] ?? '') === 'approved');
        if (!$isHost) {
            return ['error' => 'Host verification required'];
        }

        $existing = $this->col('host_seven_day_tasks')
            ->where('user_id', $uid)
            ->where('status', 'active')
            ->first();
        if ($existing) return $this->arr($existing);

        $now = now();
        $days = [];
        for ($i = 1; $i <= 7; $i++) {
            $days[] = [
                'day' => $i,
                'required_minutes' => 120,
                'completed_minutes' => 0,
                'required_calls' => 5,
                'completed_calls' => 0,
                'completed' => false,
            ];
        }
        $task = [
            'user_id' => $uid,
            'real_id' => $userArr['userId'] ?? $userArr['real_id'] ?? $userArr['appId'] ?? $uid,
            'name' => $userArr['name'] ?? 'RRR Host',
            'type' => 'free_7_day_host_task',
            'status' => 'active',
            'started_at' => $now,
            'expires_at' => $now->copy()->addDays(7),
            'reward' => 5000,
            'target_amount' => 115000,
            'total_minutes' => 0,
            'total_calls' => 0,
            'total_earnings' => 0,
            'withdrawal_demand' => 0,
            'auto_withdrawal_created' => false,
            'days' => $days,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        $id = $this->col('host_seven_day_tasks')->insertGetId($task);
        $task['_id'] = (string) $id;
        return $task;
    }

    private function evaluateHostTask(array $task): array
    {
        $days = $task['days'] ?? [];
        $allDailyComplete = true;
        foreach ($days as $d) {
            if (!($d['completed'] ?? false)) { $allDailyComplete = false; break; }
        }
        $targetComplete = (int)($task['total_earnings'] ?? 0) >= (int)($task['target_amount'] ?? 115000);
        $expired = isset($task['expires_at']) ? now()->greaterThan(
            $task['expires_at'] instanceof \DateTimeInterface ? $task['expires_at'] : \Carbon\Carbon::parse($task['expires_at'])
        ) : false;

        $task['daily_completed'] = $allDailyComplete;
        $task['target_completed'] = $targetComplete;
        $task['eligible_for_reward'] = $allDailyComplete;
        $task['eligible_for_auto_withdrawal'] = $allDailyComplete && $targetComplete;
        if ($expired && !$targetComplete) {
            $task['status'] = 'failed_target';
            $task['withdrawal_demand'] = 0;
        }
        if ($allDailyComplete && !$expired) {
            $task['reward_status'] = 'earned';
        }
        return $task;
    }

    public function hostCurrentTask(Request $request)
    {
        $uid = $this->uid($request);
        $task = $this->findOrCreateHostSevenDayTask($uid);
        if (isset($task['error'])) return response()->json(['success'=>false,'message'=>$task['error']], 422);
        $task = $this->evaluateHostTask($task);
        $this->col('host_seven_day_tasks')->where('_id', $this->oid($task['_id'] ?? $task['id'] ?? ''))->update([
            'daily_completed' => $task['daily_completed'] ?? false,
            'target_completed' => $task['target_completed'] ?? false,
            'eligible_for_reward' => $task['eligible_for_reward'] ?? false,
            'eligible_for_auto_withdrawal' => $task['eligible_for_auto_withdrawal'] ?? false,
            'status' => $task['status'] ?? 'active',
            'withdrawal_demand' => $task['withdrawal_demand'] ?? 0,
            'updated_at' => now(),
        ]);
        return response()->json(['success'=>true,'task'=>$task]);
    }

    public function hostTaskProgress(Request $request)
    {
        $data = $request->validate([
            'day' => 'nullable|integer|min:1|max:7',
            'live_minutes' => 'nullable|integer|min:0',
            'call_received' => 'nullable|integer|min:0',
            'earnings' => 'nullable|integer|min:0',
        ]);
        $uid = $this->uid($request);
        $task = $this->findOrCreateHostSevenDayTask($uid);
        if (isset($task['error'])) return response()->json(['success'=>false,'message'=>$task['error']], 422);
        $dayNo = (int)($data['day'] ?? min(7, max(1, now()->diffInDays(\Carbon\Carbon::parse($task['started_at'])) + 1)));
        $addMinutes = (int)($data['live_minutes'] ?? 0);
        $addCalls = (int)($data['call_received'] ?? 0);
        $addEarnings = (int)($data['earnings'] ?? 0);
        $days = $task['days'] ?? [];
        foreach ($days as &$d) {
            if ((int)($d['day'] ?? 0) === $dayNo) {
                $d['completed_minutes'] = min(120, (int)($d['completed_minutes'] ?? 0) + $addMinutes);
                $d['completed_calls'] = min(5, (int)($d['completed_calls'] ?? 0) + $addCalls);
                $d['completed'] = ((int)$d['completed_minutes'] >= 120) && ((int)$d['completed_calls'] >= 5);
                $d['updated_at'] = now();
                break;
            }
        }
        unset($d);
        $totalMinutes = array_sum(array_map(fn($d)=>(int)($d['completed_minutes'] ?? 0), $days));
        $totalCalls = array_sum(array_map(fn($d)=>(int)($d['completed_calls'] ?? 0), $days));
        $totalEarnings = (int)($task['total_earnings'] ?? 0) + $addEarnings;
        $allDailyComplete = collect($days)->every(fn($d)=>($d['completed'] ?? false) === true);
        $targetComplete = $totalEarnings >= (int)($task['target_amount'] ?? 115000);
        $update = [
            'days' => $days,
            'total_minutes' => $totalMinutes,
            'total_calls' => $totalCalls,
            'total_earnings' => $totalEarnings,
            'daily_completed' => $allDailyComplete,
            'target_completed' => $targetComplete,
            'eligible_for_reward' => $allDailyComplete,
            'eligible_for_auto_withdrawal' => $allDailyComplete && $targetComplete,
            'updated_at' => now(),
        ];
        if ($allDailyComplete && ($task['reward_status'] ?? '') !== 'credited') {
            $update['reward_status'] = 'earned';
        }
        $this->col('host_seven_day_tasks')->where('_id', $this->oid($task['_id'] ?? ''))->update($update);
        $this->col('host_task_progress_logs')->insert([
            'user_id'=>$uid,'task_id'=>(string)($task['_id'] ?? ''),'day'=>$dayNo,
            'live_minutes'=>$addMinutes,'call_received'=>$addCalls,'earnings'=>$addEarnings,'created_at'=>now()
        ]);
        $this->broadcastUser((string)$uid, 'host.task.updated', ['task_id'=>(string)($task['_id'] ?? ''),'day'=>$dayNo,'update'=>$update]);
        return $this->hostCurrentTask($request);
    }

    public function hostTaskSettle(Request $request)
    {
        $uid = $this->uid($request);
        $task = $this->findOrCreateHostSevenDayTask($uid);
        if (isset($task['error'])) return response()->json(['success'=>false,'message'=>$task['error']], 422);
        $task = $this->evaluateHostTask($task);
        $taskId = (string)($task['_id'] ?? '');
        if (($task['eligible_for_reward'] ?? false) && (($task['reward_status'] ?? '') !== 'credited')) {
            $reward = (int)($task['reward'] ?? 5000);
            $this->col('wallet')->where('firebase_uid', $uid)->orWhere('user_id', $uid)->increment('coins', $reward);
            $this->col('users')->where('firebase_uid', $uid)->orWhere('userId', $uid)->orWhere('real_id', $uid)->increment('coins', $reward);
            $this->col('wallet_transactions')->insert([
                'user_id'=>$uid,'type'=>'host_task_reward','coins'=>$reward,'status'=>'success','task_id'=>$taskId,'created_at'=>now()
            ]);
            $this->col('host_seven_day_tasks')->where('_id', $this->oid($taskId))->update(['reward_status'=>'credited','reward_credited_at'=>now(),'updated_at'=>now()]);
        }
        if ($task['eligible_for_auto_withdrawal'] ?? false) {
            $exists = $this->col('withdraws')->where('user_id',$uid)->where('task_id',$taskId)->first();
            if (!$exists) {
                $amount = (int)($task['total_earnings'] ?? 0);
                $this->col('withdraws')->insert([
                    'user_id'=>$uid,'task_id'=>$taskId,'amount'=>$amount,'status'=>'pending','type'=>'host_task_auto_withdrawal','created_at'=>now(),'updated_at'=>now()
                ]);
                $this->col('host_seven_day_tasks')->where('_id', $this->oid($taskId))->update([
                    'auto_withdrawal_created'=>true,'withdrawal_demand'=>$amount,'status'=>'completed','completed_at'=>now(),'updated_at'=>now()
                ]);
            }
        } elseif (($task['status'] ?? '') === 'failed_target') {
            $this->col('host_seven_day_tasks')->where('_id', $this->oid($taskId))->update(['withdrawal_demand'=>0,'updated_at'=>now()]);
        }
        $fresh = $this->col('host_seven_day_tasks')->where('_id', $this->oid($taskId))->first();
        return response()->json(['success'=>true,'task'=>$this->arr($fresh ?? $task)]);
    }

    private function appSettings(): array
    {
        return $this->arr($this->col('settings')->first() ?? []);
    }

    private function coinsForAmount(float $amount): int
    {
        $settings = $this->appSettings();
        $rate = (int) ($settings['coins_per_rupee'] ?? 10);
        return max(0, (int) floor($amount * max(1, $rate)));
    }

    public function paymentConfig()
    {
        $settings = $this->appSettings();
        return response()->json([
            'success' => true,
            'payment' => [
                'paytm_qr_enabled' => (int)($settings['paytm_qr_enabled'] ?? 1) === 1,
                'paytm_upi_id' => $settings['paytm_upi_id'] ?? '',
                'paytm_merchant_name' => $settings['paytm_merchant_name'] ?? 'RRR Voice Chat',
                'paytm_qr_url' => $settings['paytm_qr_url'] ?? '',
                'razorpay_enabled' => (int)($settings['razorpay_enabled'] ?? 0) === 1,
                'razorpay_key_id' => $settings['razorpay_key_id'] ?? '',
                'coins_per_rupee' => (int)($settings['coins_per_rupee'] ?? 10),
                'min_recharge_amount' => (int)($settings['min_recharge_amount'] ?? 10),
            ],
        ]);
    }

    public function createPaytmQrRecharge(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);
        $settings = $this->appSettings();
        if ((int)($settings['paytm_qr_enabled'] ?? 1) !== 1) {
            return response()->json(['success'=>false,'message'=>'Paytm QR disabled'], 422);
        }
        $amount = (float) $data['amount'];
        $coins = $this->coinsForAmount($amount);
        $uid = $this->uid($request);
        $orderId = 'PTM'.date('YmdHis').random_int(1000,9999);
        $upi = $settings['paytm_upi_id'] ?? '';
        $merchant = $settings['paytm_merchant_name'] ?? 'RRR Voice Chat';
        $upiLink = $upi ? 'upi://pay?pa='.urlencode($upi).'&pn='.urlencode($merchant).'&am='.urlencode((string)$amount).'&cu=INR&tn='.urlencode($orderId) : '';

        $id = $this->col('recharge_requests')->insertGetId([
            'order_id' => $orderId,
            'firebase_uid' => $uid,
            'user_id' => $uid,
            'gateway' => 'paytm_qr',
            'amount' => $amount,
            'coins' => $coins,
            'status' => 'pending',
            'upi_link' => $upiLink,
            'qr_url' => $settings['paytm_qr_url'] ?? '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success'=>true,
            'request_id'=>(string)$id,
            'order_id'=>$orderId,
            'amount'=>$amount,
            'coins'=>$coins,
            'upi_link'=>$upiLink,
            'qr_url'=>$settings['paytm_qr_url'] ?? '',
            'message'=>'Payment karne ke baad UTR submit karein. Admin approval ke baad coins add honge.',
        ]);
    }

    public function submitPaytmUtr(Request $request)
    {
        $data = $request->validate([
            'request_id' => 'required|string',
            'utr' => 'required|string|max:80',
            'screenshot_url' => 'nullable|string',
        ]);
        $this->col('recharge_requests')->where('_id', $this->oid($data['request_id']))->update([
            'utr' => $data['utr'],
            'screenshot_url' => $data['screenshot_url'] ?? '',
            'status' => 'pending',
            'updated_at' => now(),
        ]);
        return response()->json(['success'=>true,'message'=>'UTR submit ho gaya. Admin approval pending hai.']);
    }

    public function createRazorpayOrder(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);
        $settings = $this->appSettings();
        if ((int)($settings['razorpay_enabled'] ?? 0) !== 1) {
            return response()->json(['success'=>false,'message'=>'Razorpay disabled'], 422);
        }
        $keyId = $settings['razorpay_key_id'] ?? '';
        $keySecret = $settings['razorpay_key_secret'] ?? '';
        $amount = (float) $data['amount'];
        $coins = $this->coinsForAmount($amount);
        $uid = $this->uid($request);
        $receipt = 'RZP'.date('YmdHis').random_int(1000,9999);
        $order = null;

        if ($keyId && $keySecret) {
            try {
                $res = Http::withBasicAuth($keyId, $keySecret)->post('https://api.razorpay.com/v1/orders', [
                    'amount' => (int) round($amount * 100),
                    'currency' => 'INR',
                    'receipt' => $receipt,
                    'payment_capture' => 1,
                    'notes' => ['firebase_uid' => $uid, 'coins' => $coins],
                ]);
                if ($res->successful()) {
                    $order = $res->json();
                } else {
                    return response()->json(['success'=>false,'message'=>'Razorpay order failed','details'=>$res->json()], 422);
                }
            } catch (Throwable $e) {
                return response()->json(['success'=>false,'message'=>'Razorpay connection failed: '.$e->getMessage()], 500);
            }
        }

        $id = $this->col('recharge_requests')->insertGetId([
            'order_id' => $order['id'] ?? $receipt,
            'firebase_uid' => $uid,
            'user_id' => $uid,
            'gateway' => 'razorpay',
            'amount' => $amount,
            'coins' => $coins,
            'status' => $order ? 'created' : 'pending_config',
            'razorpay_order' => $order,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success'=>true,
            'request_id'=>(string)$id,
            'key_id'=>$keyId,
            'order_id'=>$order['id'] ?? $receipt,
            'amount'=>$amount,
            'amount_paise'=>(int) round($amount * 100),
            'currency'=>'INR',
            'coins'=>$coins,
            'order'=>$order,
            'message'=>$order ? 'Razorpay order created' : 'Razorpay keys baad me add karne par live order banega',
        ]);
    }

    public function verifyRazorpayPayment(Request $request)
    {
        $data = $request->validate([
            'request_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);
        $settings = $this->appSettings();
        $secret = $settings['razorpay_key_secret'] ?? '';
        if (!$secret) return response()->json(['success'=>false,'message'=>'Razorpay secret missing'], 422);

        $payload = $data['razorpay_order_id'].'|'.$data['razorpay_payment_id'];
        $expected = hash_hmac('sha256', $payload, $secret);
        if (!hash_equals($expected, $data['razorpay_signature'])) {
            return response()->json(['success'=>false,'message'=>'Invalid Razorpay signature'], 422);
        }

        $row = $this->col('recharge_requests')->where('_id', $this->oid($data['request_id']))->first();
        if (!$row) return response()->json(['success'=>false,'message'=>'Recharge request not found'], 404);

        $coins = (int)($row->coins ?? 0);
        $uid = $row->firebase_uid ?? $this->uid($request);
        $this->col('recharge_requests')->where('_id', $this->oid($data['request_id']))->update([
            'status'=>'approved',
            'payment_id'=>$data['razorpay_payment_id'],
            'verified_at'=>now(),
            'updated_at'=>now(),
        ]);
        $this->col('wallet')->where('firebase_uid', $uid)->increment('coins', $coins);
        $this->col('users')->where('firebase_uid', $uid)->increment('coins', $coins);
        $this->col('wallet_transactions')->insert([
            'user_id'=>$uid,'type'=>'recharge','gateway'=>'razorpay','amount'=>(float)($row->amount ?? 0),
            'coins'=>$coins,'status'=>'success','payment_id'=>$data['razorpay_payment_id'],'created_at'=>now()
        ]);
        return response()->json(['success'=>true,'coins'=>$coins]);
    }

    public function razorpayWebhook(Request $request)
    {
        // Optional production webhook. Signature check can be enabled after adding webhook secret.
        $payload = $request->all();
        $this->col('payment_webhook_logs')->insert([
            'gateway'=>'razorpay',
            'payload'=>$payload,
            'headers'=>$request->headers->all(),
            'created_at'=>now(),
        ]);
        return response()->json(['success'=>true]);
    }


    public function supportTickets(Request $request)
    {
        $uid = $this->uid($request);
        if (!$uid) return response()->json(['success'=>false,'message'=>'User missing'], 401);
        $tickets = $this->col('support_tickets')
            ->where('user_id', $uid)
            ->orderBy('_id', 'desc')
            ->limit(100)
            ->get()
            ->map(fn($i) => $this->arr($i))
            ->values();
        return response()->json(['success'=>true, 'tickets'=>$tickets]);
    }

    public function createSupportTicket(Request $request)
    {
        $uid = $this->uid($request);
        if (!$uid) return response()->json(['success'=>false,'message'=>'User missing'], 401);
        $data = $request->validate([
            'subject' => 'required|string|max:160',
            'category' => 'nullable|string|max:60',
            'priority' => 'nullable|in:normal,high,urgent',
            'message' => 'required|string|max:3000',
        ]);
        $user = $this->findUser($uid);
        $ticket = [
            'ticket_no' => 'CS'.date('ymd').strtoupper(substr(uniqid(), -6)),
            'user_id' => $uid,
            'real_id' => $user->real_id ?? $user->userId ?? '',
            'user_name' => $user->name ?? 'RRR User',
            'user_email' => $user->email ?? '',
            'subject' => $data['subject'],
            'category' => $data['category'] ?? 'Other',
            'priority' => $data['priority'] ?? 'normal',
            'status' => 'open',
            'last_message' => $data['message'],
            'last_sender_type' => 'user',
            'unread_admin' => 1,
            'unread_user' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $id = $this->col('support_tickets')->insertGetId($ticket);
        $message = [
            'ticket_id' => (string)$id,
            'user_id' => $uid,
            'sender_id' => $uid,
            'sender_type' => 'user',
            'message' => $data['message'],
            'read_by_user' => true,
            'read_by_admin' => false,
            'created_at' => now(),
        ];
        $this->col('support_messages')->insert($message);
        $ticket['_id'] = (string)$id;
        $this->broadcastPublic('admin.support', 'support.ticket.created', ['ticket'=>$ticket]);
        return response()->json(['success'=>true, 'ticket'=>$ticket]);
    }

    public function supportTicketMessages(Request $request, $ticketId)
    {
        $uid = $this->uid($request);
        $ticket = $this->col('support_tickets')->where('_id', $this->oid($ticketId))->first();
        if (!$ticket || (($ticket->user_id ?? '') !== $uid)) {
            return response()->json(['success'=>false,'message'=>'Ticket not found'], 404);
        }
        $this->col('support_tickets')->where('_id', $this->oid($ticketId))->update(['unread_user'=>0, 'updated_at'=>now()]);
        $messages = $this->col('support_messages')
            ->where('ticket_id', (string)$ticketId)
            ->orderBy('_id', 'asc')
            ->limit(500)
            ->get()
            ->map(fn($i) => $this->arr($i))
            ->values();
        return response()->json(['success'=>true, 'ticket'=>$this->arr($ticket), 'messages'=>$messages]);
    }

    public function sendSupportTicketMessage(Request $request, $ticketId)
    {
        $uid = $this->uid($request);
        $data = $request->validate(['message'=>'required|string|max:3000']);
        $ticket = $this->col('support_tickets')->where('_id', $this->oid($ticketId))->first();
        if (!$ticket || (($ticket->user_id ?? '') !== $uid)) {
            return response()->json(['success'=>false,'message'=>'Ticket not found'], 404);
        }
        if (($ticket->status ?? 'open') === 'closed') {
            return response()->json(['success'=>false,'message'=>'Ticket closed. New ticket create karo.'], 422);
        }
        $message = [
            'ticket_id' => (string)$ticketId,
            'user_id' => $uid,
            'sender_id' => $uid,
            'sender_type' => 'user',
            'message' => $data['message'],
            'read_by_user' => true,
            'read_by_admin' => false,
            'created_at' => now(),
        ];
        $mid = $this->col('support_messages')->insertGetId($message);
        $message['_id'] = (string)$mid;
        $this->col('support_tickets')->where('_id', $this->oid($ticketId))->update([
            'status' => 'open',
            'last_message' => $data['message'],
            'last_sender_type' => 'user',
            'unread_admin' => (int)($ticket->unread_admin ?? 0) + 1,
            'updated_at' => now(),
        ]);
        $this->broadcastPublic('admin.support', 'support.message.user', ['ticket_id'=>(string)$ticketId, 'message'=>$message]);
        return response()->json(['success'=>true, 'message'=>$message]);
    }


    // ------------------------------------------------------------------
    // REAL ID SEARCH + COIN SELLER PRODUCTION APIs
    // ------------------------------------------------------------------
    public function searchUsersByRealId(Request $request)
    {
        $realId = trim((string)($request->query('real_id') ?: $request->query('q') ?: $request->input('real_id') ?: $request->input('q')));
        if ($realId === '') return response()->json(['success'=>true,'users'=>[]]);
        $users = $this->col('users')
            ->where('real_id', 'like', $realId.'%')
            ->orWhere('userId', 'like', $realId.'%')
            ->orWhere('appId', 'like', $realId.'%')
            ->limit(20)->get()->map(fn($u)=>$this->publicUserPayload($u))->values();
        return response()->json(['success'=>true,'users'=>$users]);
    }

    public function coinSellerList()
    {
        $sellers = $this->col('coin_sellers')
            ->where('status','active')
            ->whereIn('seller_type', ['medium','super'])
            ->orderBy('seller_type','desc')
            ->limit(100)->get()->map(fn($i)=>$this->arr($i))->values();
        return response()->json(['success'=>true,'sellers'=>$sellers]);
    }

    public function coinSellerMe(Request $request)
    {
        $user = $this->findUser($this->uid($request));
        if (!$user) return response()->json(['success'=>false,'message'=>'User not found'],404);
        $realId = $this->realIdOf($user);
        $seller = $this->col('coin_sellers')->where('real_id',$realId)->first();
        if (!$seller || (($seller->status ?? '') !== 'active')) return response()->json(['success'=>false,'message'=>'Coin Seller Center not active'],403);
        return response()->json(['success'=>true,'seller'=>$this->arr($seller),'user'=>$this->publicUserPayload($user)]);
    }

    public function adminCoinSellers(Request $request)
    {
        $query = $this->col('coin_sellers')->orderBy('_id','desc');
        if ($request->query('status')) $query->where('status',$request->query('status'));
        if ($request->query('type')) $query->where('seller_type',$request->query('type'));
        return response()->json(['success'=>true,'sellers'=>$query->limit(500)->get()->map(fn($i)=>$this->arr($i))->values()]);
    }

    public function adminCreateCoinSeller(Request $request)
    {
        $data = $request->validate([
            'real_id'=>'required|string',
            'mobile'=>'required|string|max:20',
            'whatsapp'=>'nullable|string|max:20',
            'seller_type'=>'required|in:normal,medium,super',
            'seller_name'=>'nullable|string|max:100',
            'bio'=>'nullable|string|max:300',
            'initial_coins'=>'nullable|integer|min:0',
            'status'=>'nullable|in:active,deactive,inactive',
        ]);
        $user = $this->findUser($data['real_id']);
        if (!$user) return response()->json(['success'=>false,'message'=>'User Real ID not found'],404);
        $realId = $this->realIdOf($user);
        $payload = [
            'user_id'=>(string)($user->_id ?? ''),
            'real_id'=>$realId,
            'seller_type'=>$data['seller_type'],
            'seller_name'=>$data['seller_name'] ?? ($user->name ?? 'Coin Seller'),
            'mobile'=>$data['mobile'],
            'whatsapp'=>$data['whatsapp'] ?? $data['mobile'],
            'bio'=>$data['bio'] ?? '',
            'coin_balance'=>(int)($data['initial_coins'] ?? 0),
            'can_sell'=>true,
            'can_withdraw'=>$data['seller_type'] === 'super',
            'show_public'=>in_array($data['seller_type'], ['medium','super']),
            'show_tag'=>true,
            'status'=>$data['status'] ?? 'active',
            'updated_at'=>now(),
        ];
        $exists = $this->col('coin_sellers')->where('real_id',$realId)->first();
        if ($exists) $this->col('coin_sellers')->where('real_id',$realId)->update($payload);
        else { $payload['created_at']=now(); $this->col('coin_sellers')->insert($payload); }
        $this->col('users')->where('real_id',$realId)->orWhere('userId',$realId)->orWhere('appId',$realId)->update([
            'is_coin_seller'=>true,
            'seller_type'=>$data['seller_type'],
            'coin_seller_status'=>$payload['status'],
            'coin_seller_mobile'=>$data['mobile'],
            'coin_seller_whatsapp'=>$payload['whatsapp'],
            'updated_at'=>now(),
        ]);
        $this->sendSystemMsg($realId, 'coin_seller_activated', 'Coin Seller Activated', 'Aapka Coin Seller Center active ho gaya hai.', ['seller_type'=>$data['seller_type']]);
        return response()->json(['success'=>true,'seller'=>$payload]);
    }

    public function adminCoinSellerCoins(Request $request, $realId)
    {
        $data = $request->validate(['action'=>'required|in:add,deduct','coins'=>'required|integer|min:1','note'=>'nullable|string|max:200']);
        $seller = $this->col('coin_sellers')->where('real_id',(string)$realId)->first();
        if (!$seller) return response()->json(['success'=>false,'message'=>'Coin seller not found'],404);
        $coins = (int)$data['coins'];
        if ($data['action'] === 'add') $this->col('coin_sellers')->where('real_id',(string)$realId)->increment('coin_balance',$coins);
        else {
            if ((int)($seller->coin_balance ?? 0) < $coins) return response()->json(['success'=>false,'message'=>'Seller balance low'],422);
            $this->col('coin_sellers')->where('real_id',(string)$realId)->decrement('coin_balance',$coins);
        }
        $this->col('coin_seller_transactions')->insert(['seller_real_id'=>(string)$realId,'type'=>'admin_'.$data['action'],'coins'=>$coins,'note'=>$data['note'] ?? '', 'created_at'=>now()]);
        $this->sendSystemMsg((string)$realId, 'coin_seller_balance', 'Coin Seller Balance Updated', ($data['action']==='add'?'Added ':'Deducted ').$coins.' coins.', ['coins'=>$coins]);
        return response()->json(['success'=>true]);
    }

    public function adminCoinSellerStatus(Request $request, $realId)
    {
        $data = $request->validate(['status'=>'required|in:active,deactive,inactive']);
        $this->col('coin_sellers')->where('real_id',(string)$realId)->update(['status'=>$data['status'],'updated_at'=>now()]);
        $this->col('users')->where('real_id',(string)$realId)->orWhere('userId',(string)$realId)->orWhere('appId',(string)$realId)->update(['coin_seller_status'=>$data['status'],'updated_at'=>now()]);
        return response()->json(['success'=>true]);
    }

    public function coinSellerTransfer(Request $request)
    {
        $sellerUser = $this->findUser($this->uid($request));
        if (!$sellerUser) return response()->json(['success'=>false,'message'=>'Seller user missing'],401);
        $sellerRealId = $this->realIdOf($sellerUser);
        $seller = $this->col('coin_sellers')->where('real_id',$sellerRealId)->where('status','active')->first();
        if (!$seller) return response()->json(['success'=>false,'message'=>'Coin Seller Center not active'],403);
        $data = $request->validate(['to_real_id'=>'required|string','coins'=>'required|integer|min:1']);
        $receiver = $this->findUser($data['to_real_id']);
        if (!$receiver) return response()->json(['success'=>false,'message'=>'Receiver Real ID not found'],404);
        $coins = (int)$data['coins'];
        if ((int)($seller->coin_balance ?? 0) < $coins) return response()->json(['success'=>false,'message'=>'Seller coin balance low'],422);
        $receiverRealId = $this->realIdOf($receiver);
        $this->col('coin_sellers')->where('real_id',$sellerRealId)->decrement('coin_balance',$coins);
        $this->col('users')->where('real_id',$receiverRealId)->orWhere('userId',$receiverRealId)->orWhere('appId',$receiverRealId)->increment('coins',$coins);
        $txn = ['seller_real_id'=>$sellerRealId,'seller_type'=>$seller->seller_type ?? 'normal','receiver_real_id'=>$receiverRealId,'coins'=>$coins,'type'=>'seller_transfer','status'=>'success','created_at'=>now()];
        $txnId = $this->col('coin_seller_transactions')->insertGetId($txn);
        $showSeller = in_array(($seller->seller_type ?? 'normal'), ['medium','super']);
        $body = $showSeller
            ? 'Seller: '.($seller->seller_name ?? 'Coin Seller').' | ID: '.$sellerRealId.' | Phone: '.($seller->mobile ?? '').' | Amount: '.$coins.' Coins | Bio: '.($seller->bio ?? '')
            : 'Amount: '.$coins.' Coins added to your wallet.';
        $this->sendSystemMsg($receiverRealId, $showSeller ? 'coin_seller_recharge' : 'coin_credit', 'Coin Recharge Successful', $body, ['transaction_id'=>(string)$txnId,'coins'=>$coins,'seller'=>$showSeller?$this->arr($seller):null]);
        $this->sendSystemMsg($sellerRealId, 'coin_seller_sale', 'Coins Sold', $coins.' coins sent to '.$receiverRealId, ['transaction_id'=>(string)$txnId]);
        return response()->json(['success'=>true,'transaction_id'=>(string)$txnId]);
    }

    public function coinSellerWithdrawalRequest(Request $request)
    {
        $sellerUser = $this->findUser($this->uid($request));
        if (!$sellerUser) return response()->json(['success'=>false,'message'=>'Seller user missing'],401);
        $sellerRealId = $this->realIdOf($sellerUser);
        $seller = $this->col('coin_sellers')->where('real_id',$sellerRealId)->where('status','active')->first();
        if (!$seller || (($seller->seller_type ?? 'normal') !== 'super')) return response()->json(['success'=>false,'message'=>'Only Super Coin Seller can withdraw'],403);
        $data = $request->validate(['coins'=>'required|integer|min:1','method'=>'required|string|max:30','account'=>'required|string|max:120']);
        if ((int)($seller->coin_balance ?? 0) < (int)$data['coins']) return response()->json(['success'=>false,'message'=>'Balance low'],422);
        $id = $this->col('coin_seller_withdrawals')->insertGetId(['seller_real_id'=>$sellerRealId,'coins'=>(int)$data['coins'],'method'=>$data['method'],'account'=>$data['account'],'status'=>'pending','created_at'=>now(),'updated_at'=>now()]);
        return response()->json(['success'=>true,'withdrawal_id'=>(string)$id]);
    }

}
