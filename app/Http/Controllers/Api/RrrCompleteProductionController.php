<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\ObjectId;
use Throwable;
use App\Events\AppRealtimeEvent;

class RrrCompleteProductionController extends Controller
{
    private function col(string $name) { return DB::connection('mongodb')->table($name); }
    private function oid($id) { try { return new ObjectId($id); } catch (Throwable $e) { return $id; } }
    private function arr($item) { return json_decode(json_encode($item), true) ?: []; }
    private function uid(Request $request) { return $request->header('X-User-Id') ?: $request->input('uid') ?: $request->input('firebase_uid') ?: $request->input('user_id'); }
    private function now() { return now(); }

    private function broadcast(string $channel, string $event, array $payload = []): void
    {
        try { event(new AppRealtimeEvent($channel, $event, $payload)); } catch (Throwable $e) { report($e); }
    }

    private function wallet(string $uid): array
    {
        $wallet = $this->col('wallet')->where('firebase_uid', $uid)->orWhere('user_id', $uid)->first();
        if (!$wallet) {
            $this->col('wallet')->insert(['firebase_uid'=>$uid,'user_id'=>$uid,'coins'=>0,'diamonds'=>0,'withdrawable'=>0,'created_at'=>$this->now(),'updated_at'=>$this->now()]);
            $wallet = $this->col('wallet')->where('firebase_uid', $uid)->orWhere('user_id', $uid)->first();
        }
        return $this->arr($wallet);
    }

    private function debitCoins(string $uid, int $coins, string $reason, array $meta = [])
    {
        if ($coins <= 0) return [true, $this->wallet($uid)];
        $wallet = $this->wallet($uid);
        if ((int)($wallet['coins'] ?? 0) < $coins) return [false, $wallet];
        $this->col('wallet')->where('firebase_uid', $uid)->orWhere('user_id', $uid)->decrement('coins', $coins);
        $this->col('wallet_transactions')->insert(['user_id'=>$uid,'type'=>'debit','coins'=>$coins,'reason'=>$reason,'meta'=>$meta,'created_at'=>$this->now()]);
        return [true, $this->wallet($uid)];
    }

    public function productionSummary(Request $request)
    {
        return response()->json(['success'=>true,'modules'=>[
            'auth'=>'firebase/google/phone/email/real_id','profile'=>'edit_dp_bio_5_photos','home'=>'bigo_mico_grid','messages'=>'api_reverb','moments'=>'feed_like_comment','agency_bd'=>'in_app_otp','verification'=>'mlkit_admin_review','support'=>'ticket_chat','system_messages'=>'private_user_channel','voice_room'=>'5_layouts_gift_chat_settings','vip_store'=>'plans_frames_bubbles_badges','realtime'=>'laravel_reverb'
        ]]);
    }

    public function roomLayouts()
    {
        $items = $this->col('room_layouts')->orderBy('sort_order','asc')->get()->map(fn($i)=>$this->arr($i))->values();
        if ($items->isEmpty()) {
            $seed = [
                ['key'=>'classic','name'=>'Classic','type'=>'free','price'=>0,'seats'=>9,'sort_order'=>1,'description'=>'1 host + 9 seats classic voice layout'],
                ['key'=>'party','name'=>'Party','type'=>'free','price'=>0,'seats'=>8,'sort_order'=>2,'description'=>'Party style grid layout'],
                ['key'=>'vip','name'=>'VIP','type'=>'paid','price'=>5000,'seats'=>10,'sort_order'=>3,'description'=>'Premium VIP layout with gold seats'],
                ['key'=>'pk','name'=>'PK Battle','type'=>'paid','price'=>15000,'seats'=>10,'sort_order'=>4,'description'=>'Team A vs Team B PK layout'],
                ['key'=>'luxury','name'=>'Luxury','type'=>'paid','price'=>25000,'seats'=>12,'sort_order'=>5,'description'=>'Luxury crown room layout'],
            ];
            foreach ($seed as $row) $this->col('room_layouts')->insert($row + ['status'=>'active','created_at'=>$this->now(),'updated_at'=>$this->now()]);
            $items = $this->col('room_layouts')->orderBy('sort_order','asc')->get()->map(fn($i)=>$this->arr($i))->values();
        }
        return response()->json(['success'=>true,'layouts'=>$items]);
    }

    public function buyRoomLayout(Request $request)
    {
        $uid = $this->uid($request); if (!$uid) return response()->json(['success'=>false,'message'=>'User missing'],401);
        $data = $request->validate(['layout_id'=>'nullable|string','layout_key'=>'nullable|string']);
        $q = $this->col('room_layouts');
        $layout = !empty($data['layout_id']) ? $q->where('_id',$this->oid($data['layout_id']))->first() : $q->where('key',$data['layout_key'] ?? '')->first();
        if (!$layout) return response()->json(['success'=>false,'message'=>'Layout not found'],404);
        $layoutArr = $this->arr($layout); $price=(int)($layoutArr['price'] ?? 0);
        $existing = $this->col('room_layout_purchases')->where('user_id',$uid)->where('layout_key',$layoutArr['key'] ?? '')->first();
        if ($existing || $price<=0 || ($layoutArr['type'] ?? '')==='free') return response()->json(['success'=>true,'already'=>!!$existing,'layout'=>$layoutArr]);
        [$ok,$wallet]=$this->debitCoins($uid,$price,'room_layout_purchase',['layout'=>$layoutArr['key'] ?? '']);
        if (!$ok) return response()->json(['success'=>false,'message'=>'Insufficient coins','wallet'=>$wallet],422);
        $purchase=['user_id'=>$uid,'layout_id'=>(string)($layoutArr['_id'] ?? ''),'layout_key'=>$layoutArr['key'] ?? '','price'=>$price,'status'=>'active','created_at'=>$this->now()];
        $this->col('room_layout_purchases')->insert($purchase);
        $this->broadcast('user.'.$uid,'store.purchase',['purchase'=>$purchase]);
        return response()->json(['success'=>true,'purchase'=>$purchase,'wallet'=>$this->wallet($uid)]);
    }

    public function myRoomLayouts(Request $request)
    {
        $uid=$this->uid($request); if(!$uid) return response()->json(['success'=>false,'message'=>'User missing'],401);
        return response()->json(['success'=>true,'purchases'=>$this->col('room_layout_purchases')->where('user_id',$uid)->get()->map(fn($i)=>$this->arr($i))->values()]);
    }

    public function updateRoomSettings(Request $request, $roomId)
    {
        $uid=$this->uid($request); if(!$uid) return response()->json(['success'=>false,'message'=>'User missing'],401);
        $data=$request->validate([
            'layout_key'=>'nullable|string','mute_all'=>'nullable|boolean','gift_enabled'=>'nullable|boolean','message_enabled'=>'nullable|boolean','join_sound'=>'nullable|boolean','seat_locked'=>'nullable|boolean','notice'=>'nullable|string|max:1000','password'=>'nullable|string|max:40'
        ]);
        if (!empty($data['layout_key'])) {
            $layout=$this->col('room_layouts')->where('key',$data['layout_key'])->first();
            if (!$layout) return response()->json(['success'=>false,'message'=>'Layout not found'],404);
            $layoutArr=$this->arr($layout); $price=(int)($layoutArr['price'] ?? 0);
            if ($price>0 && ($layoutArr['type'] ?? '')==='paid') {
                $p=$this->col('room_layout_purchases')->where('user_id',$uid)->where('layout_key',$data['layout_key'])->first();
                if (!$p) return response()->json(['success'=>false,'message'=>'Paid layout purchase required'],402);
            }
        }
        $payload=$data + ['room_id'=>(string)$roomId,'updated_by'=>$uid,'updated_at'=>$this->now()];
        $this->col('room_settings')->updateOrInsert(['room_id'=>(string)$roomId],$payload);
        $this->broadcast('room.'.$roomId,'room.settings.updated',['room_id'=>(string)$roomId,'settings'=>$payload]);
        return response()->json(['success'=>true,'settings'=>$payload]);
    }

    public function roomSettings($roomId)
    {
        $settings=$this->col('room_settings')->where('room_id',(string)$roomId)->first();
        return response()->json(['success'=>true,'settings'=>$this->arr($settings ?: ['room_id'=>(string)$roomId,'layout_key'=>'classic','gift_enabled'=>true,'message_enabled'=>true,'join_sound'=>true])]);
    }

    public function roomMessages($roomId)
    {
        return response()->json(['success'=>true,'messages'=>$this->col('room_messages')->where('room_id',(string)$roomId)->orderBy('_id','desc')->limit(100)->get()->map(fn($i)=>$this->arr($i))->reverse()->values()]);
    }

    public function sendRoomMessage(Request $request, $roomId)
    {
        $uid=$this->uid($request); if(!$uid) return response()->json(['success'=>false,'message'=>'User missing'],401);
        $settings=$this->arr($this->col('room_settings')->where('room_id',(string)$roomId)->first() ?: []);
        if (($settings['message_enabled'] ?? true) === false) return response()->json(['success'=>false,'message'=>'Room messages are off'],403);
        $data=$request->validate(['message'=>'required|string|max:1000']);
        $msg=['room_id'=>(string)$roomId,'user_id'=>$uid,'message'=>$data['message'],'type'=>'text','created_at'=>$this->now()];
        $id=$this->col('room_messages')->insertGetId($msg); $msg['_id']=(string)$id;
        $this->broadcast('room.'.$roomId,'room.message.sent',['message'=>$msg]);
        return response()->json(['success'=>true,'message'=>$msg]);
    }

    public function sendRoomGift(Request $request, $roomId)
    {
        $uid=$this->uid($request); if(!$uid) return response()->json(['success'=>false,'message'=>'User missing'],401);
        $settings=$this->arr($this->col('room_settings')->where('room_id',(string)$roomId)->first() ?: []);
        if (($settings['gift_enabled'] ?? true) === false) return response()->json(['success'=>false,'message'=>'Room gifts are off'],403);
        $data=$request->validate(['gift_id'=>'nullable|string','gift_name'=>'nullable|string','price'=>'nullable|integer','to_user_id'=>'nullable|string']);
        $price=(int)($data['price'] ?? 0);
        [$ok,$wallet]=$this->debitCoins($uid,$price,'room_gift',['room_id'=>(string)$roomId,'gift'=>$data['gift_name'] ?? $data['gift_id'] ?? 'gift']);
        if (!$ok) return response()->json(['success'=>false,'message'=>'Insufficient coins','wallet'=>$wallet],422);
        $gift=$data + ['room_id'=>(string)$roomId,'from_user_id'=>$uid,'created_at'=>$this->now()];
        $id=$this->col('room_gifts')->insertGetId($gift); $gift['_id']=(string)$id;
        $this->broadcast('room.'.$roomId,'room.gift.sent',['gift'=>$gift]);
        return response()->json(['success'=>true,'gift'=>$gift,'wallet'=>$this->wallet($uid)]);
    }

    public function storeCategories()
    {
        return response()->json(['success'=>true,'categories'=>['vip','room_layouts','frames','bubbles','entry_effects','badges']]);
    }

    public function vipPlans()
    {
        $plans=$this->col('vip_plans')->where('status','active')->orderBy('level','asc')->get()->map(fn($i)=>$this->arr($i))->values();
        if ($plans->isEmpty()) {
            foreach ([1=>99,2=>299,3=>599,4=>999,5=>1999,6=>4999] as $lvl=>$price) {
                $this->col('vip_plans')->insert(['level'=>$lvl,'name'=>'VIP '.$lvl,'price'=>$price,'duration_days'=>30,'status'=>'active','benefits'=>['badge','profile_frame','chat_bubble'],'created_at'=>$this->now(),'updated_at'=>$this->now()]);
            }
            $plans=$this->col('vip_plans')->where('status','active')->orderBy('level','asc')->get()->map(fn($i)=>$this->arr($i))->values();
        }
        return response()->json(['success'=>true,'plans'=>$plans]);
    }

    public function buyVipPlan(Request $request)
    {
        $uid=$this->uid($request); if(!$uid) return response()->json(['success'=>false,'message'=>'User missing'],401);
        $data=$request->validate(['plan_id'=>'nullable|string','level'=>'nullable|integer']);
        $plan=!empty($data['plan_id']) ? $this->col('vip_plans')->where('_id',$this->oid($data['plan_id']))->first() : $this->col('vip_plans')->where('level',(int)($data['level'] ?? 1))->first();
        if(!$plan) return response()->json(['success'=>false,'message'=>'VIP plan not found'],404);
        $p=$this->arr($plan); $price=(int)($p['price'] ?? 0);
        [$ok,$wallet]=$this->debitCoins($uid,$price,'vip_purchase',['level'=>$p['level'] ?? null]);
        if(!$ok) return response()->json(['success'=>false,'message'=>'Insufficient coins','wallet'=>$wallet],422);
        $purchase=['user_id'=>$uid,'plan_id'=>(string)($p['_id'] ?? ''),'level'=>(int)($p['level'] ?? 1),'price'=>$price,'status'=>'active','started_at'=>$this->now(),'expires_at'=>now()->addDays((int)($p['duration_days'] ?? 30)),'created_at'=>$this->now()];
        $this->col('vip_purchases')->insert($purchase);
        $this->col('users')->where('firebase_uid',$uid)->orWhere('user_id',$uid)->update(['vip_level'=>$purchase['level'],'vip_expires_at'=>$purchase['expires_at'],'updated_at'=>$this->now()]);
        $this->broadcast('user.'.$uid,'vip.activated',['vip'=>$purchase]);
        return response()->json(['success'=>true,'purchase'=>$purchase,'wallet'=>$this->wallet($uid)]);
    }

    public function inventory(Request $request)
    {
        $uid=$this->uid($request); if(!$uid) return response()->json(['success'=>false,'message'=>'User missing'],401);
        return response()->json(['success'=>true,'inventory'=>$this->col('user_inventory')->where('user_id',$uid)->get()->map(fn($i)=>$this->arr($i))->values(),'vip'=>$this->col('vip_purchases')->where('user_id',$uid)->orderBy('_id','desc')->limit(20)->get()->map(fn($i)=>$this->arr($i))->values(),'layouts'=>$this->col('room_layout_purchases')->where('user_id',$uid)->get()->map(fn($i)=>$this->arr($i))->values()]);
    }

    public function adminStoreUpsert(Request $request, string $type)
    {
        $data=$request->all();
        $collection = match($type) {
            'vip' => 'vip_plans', 'layout' => 'room_layouts', 'frame' => 'frames', 'bubble' => 'bubbles', 'entry' => 'entry_effects', 'badge' => 'badges', default => 'store_items'
        };
        $data['updated_at']=$this->now();
        if (!empty($data['id'])) {
            $id=$data['id']; unset($data['id']);
            $this->col($collection)->where('_id',$this->oid($id))->update($data);
            return response()->json(['success'=>true,'id'=>$id]);
        }
        $data['created_at']=$this->now(); $id=$this->col($collection)->insertGetId($data);
        return response()->json(['success'=>true,'id'=>(string)$id]);
    }

    public function adminStoreList(string $type)
    {
        $collection = match($type) {
            'vip' => 'vip_plans', 'layout' => 'room_layouts', 'frame' => 'frames', 'bubble' => 'bubbles', 'entry' => 'entry_effects', 'badge' => 'badges', default => 'store_items'
        };
        return response()->json(['success'=>true,'items'=>$this->col($collection)->orderBy('_id','desc')->limit(200)->get()->map(fn($i)=>$this->arr($i))->values()]);
    }

    // ================= NEXT PRODUCTION: REVENUE, ENGAGEMENT, SAFETY =================
    private function creditCoins(string $uid, int $coins, string $reason, array $meta = [])
    {
        if ($coins <= 0) return $this->wallet($uid);
        $this->wallet($uid);
        $this->col('wallet')->where('firebase_uid', $uid)->orWhere('user_id', $uid)->increment('coins', $coins);
        $this->col('wallet_transactions')->insert(['user_id'=>$uid,'type'=>'credit','coins'=>$coins,'reason'=>$reason,'meta'=>$meta,'created_at'=>$this->now()]);
        $this->broadcast('user.'.$uid,'wallet.updated',['wallet'=>$this->wallet($uid)]);
        return $this->wallet($uid);
    }

    public function rechargePackagesV2()
    {
        $items=$this->col('recharge_packages')->where('status','active')->orderBy('sort_order','asc')->get()->map(fn($i)=>$this->arr($i))->values();
        if ($items->isEmpty()) {
            foreach ([[99,1000,1],[199,2200,2],[499,6000,3],[999,13000,4],[1999,28000,5]] as $p) {
                $this->col('recharge_packages')->insert(['amount_inr'=>$p[0],'coins'=>$p[1],'sort_order'=>$p[2],'status'=>'active','created_at'=>$this->now(),'updated_at'=>$this->now()]);
            }
            $items=$this->col('recharge_packages')->where('status','active')->orderBy('sort_order','asc')->get()->map(fn($i)=>$this->arr($i))->values();
        }
        return response()->json(['success'=>true,'packages'=>$items]);
    }

    public function createRecharge(Request $request)
    {
        $uid=$this->uid($request); if(!$uid) return response()->json(['success'=>false,'message'=>'User missing'],401);
        $data=$request->validate(['package_id'=>'nullable|string','amount_inr'=>'nullable|integer','method'=>'nullable|string','utr'=>'nullable|string']);
        $pkg=!empty($data['package_id']) ? $this->col('recharge_packages')->where('_id',$this->oid($data['package_id']))->first() : null;
        $coins=$pkg ? (int)($this->arr($pkg)['coins']??0) : (int)(($data['amount_inr']??0)*10);
        $row=['user_id'=>$uid,'package_id'=>$data['package_id']??null,'amount_inr'=>$data['amount_inr']??($pkg?$this->arr($pkg)['amount_inr']:0),'coins'=>$coins,'method'=>$data['method']??'manual_upi','utr'=>$data['utr']??null,'status'=>'pending','created_at'=>$this->now(),'updated_at'=>$this->now()];
        $id=$this->col('recharges')->insertGetId($row); $row['_id']=(string)$id;
        return response()->json(['success'=>true,'recharge'=>$row]);
    }

    public function walletTransactions(Request $request)
    {
        $uid=$this->uid($request); if(!$uid) return response()->json(['success'=>false,'message'=>'User missing'],401);
        return response()->json(['success'=>true,'wallet'=>$this->wallet($uid),'transactions'=>$this->col('wallet_transactions')->where('user_id',$uid)->orderBy('_id','desc')->limit(100)->get()->map(fn($i)=>$this->arr($i))->values()]);
    }

    public function requestWithdrawal(Request $request)
    {
        $uid=$this->uid($request); if(!$uid) return response()->json(['success'=>false,'message'=>'User missing'],401);
        $data=$request->validate(['amount'=>'required|integer|min:1','method'=>'required|string','account'=>'required|string|max:200']);
        $w=$this->wallet($uid); if((int)($w['withdrawable']??0)<(int)$data['amount']) return response()->json(['success'=>false,'message'=>'Insufficient withdrawable balance','wallet'=>$w],422);
        $row=$data+['user_id'=>$uid,'status'=>'pending','created_at'=>$this->now(),'updated_at'=>$this->now()];
        $id=$this->col('withdrawals')->insertGetId($row); $row['_id']=(string)$id;
        $this->broadcast('admin.withdrawals','withdrawal.created',['withdrawal'=>$row]);
        return response()->json(['success'=>true,'withdrawal'=>$row]);
    }

    public function myWithdrawals(Request $request)
    {
        $uid=$this->uid($request); if(!$uid) return response()->json(['success'=>false,'message'=>'User missing'],401);
        return response()->json(['success'=>true,'withdrawals'=>$this->col('withdrawals')->where('user_id',$uid)->orderBy('_id','desc')->limit(100)->get()->map(fn($i)=>$this->arr($i))->values()]);
    }

    public function adminReviewWithdrawal(Request $request, $id)
    {
        $data=$request->validate(['status'=>'required|string','note'=>'nullable|string']);
        $wd=$this->arr($this->col('withdrawals')->where('_id',$this->oid($id))->first() ?: []);
        if(!$wd) return response()->json(['success'=>false,'message'=>'Withdrawal not found'],404);
        $this->col('withdrawals')->where('_id',$this->oid($id))->update(['status'=>$data['status'],'admin_note'=>$data['note']??null,'updated_at'=>$this->now()]);
        $this->broadcast('user.'.($wd['user_id']??''),'withdrawal.updated',['withdrawal_id'=>(string)$id,'status'=>$data['status']]);
        return response()->json(['success'=>true]);
    }

    public function animatedGifts()
    {
        $items=$this->col('animated_gifts')->where('status','active')->orderBy('price','asc')->get()->map(fn($i)=>$this->arr($i))->values();
        if($items->isEmpty()){
            foreach([['Rose',10],['Heart',99],['Sports Car',999],['Castle',4999],['Crown',9999]] as $g){$this->col('animated_gifts')->insert(['name'=>$g[0],'price'=>$g[1],'animation'=>'default','status'=>'active','created_at'=>$this->now(),'updated_at'=>$this->now()]);}
            $items=$this->col('animated_gifts')->where('status','active')->orderBy('price','asc')->get()->map(fn($i)=>$this->arr($i))->values();
        }
        return response()->json(['success'=>true,'gifts'=>$items]);
    }

    public function adminAnimatedGiftUpsert(Request $request){$data=$request->all();$data['updated_at']=$this->now();if(!empty($data['id'])){$id=$data['id'];unset($data['id']);$this->col('animated_gifts')->where('_id',$this->oid($id))->update($data);return response()->json(['success'=>true,'id'=>$id]);}$data['created_at']=$this->now();$id=$this->col('animated_gifts')->insertGetId($data);return response()->json(['success'=>true,'id'=>(string)$id]);}

    public function pkStatus($roomId){$b=$this->col('pk_battles')->where('room_id',(string)$roomId)->where('status','active')->orderBy('_id','desc')->first();return response()->json(['success'=>true,'battle'=>$this->arr($b?:[])]);}
    public function pkStartV2(Request $request){$data=$request->validate(['room_id'=>'required|string','team_a'=>'nullable|array','team_b'=>'nullable|array','duration_seconds'=>'nullable|integer']);$row=$data+['score_a'=>0,'score_b'=>0,'status'=>'active','started_at'=>$this->now(),'created_at'=>$this->now()];$id=$this->col('pk_battles')->insertGetId($row);$row['_id']=(string)$id;$this->broadcast('room.'.$data['room_id'],'pk.started',['battle'=>$row]);return response()->json(['success'=>true,'battle'=>$row]);}
    public function pkScoreV2(Request $request){$data=$request->validate(['battle_id'=>'required|string','room_id'=>'required|string','team'=>'required|string','score'=>'required|integer']);$field=$data['team']==='b'?'score_b':'score_a';$this->col('pk_battles')->where('_id',$this->oid($data['battle_id']))->increment($field,(int)$data['score']);$b=$this->arr($this->col('pk_battles')->where('_id',$this->oid($data['battle_id']))->first()?:[]);$this->broadcast('room.'.$data['room_id'],'pk.score',['battle'=>$b]);return response()->json(['success'=>true,'battle'=>$b]);}
    public function pkEndV2(Request $request){$data=$request->validate(['battle_id'=>'required|string','room_id'=>'required|string']);$b=$this->arr($this->col('pk_battles')->where('_id',$this->oid($data['battle_id']))->first()?:[]);$winner=(($b['score_a']??0)>=($b['score_b']??0))?'a':'b';$this->col('pk_battles')->where('_id',$this->oid($data['battle_id']))->update(['status'=>'ended','winner'=>$winner,'ended_at'=>$this->now()]);$this->broadcast('room.'.$data['room_id'],'pk.ended',['winner'=>$winner]);return response()->json(['success'=>true,'winner'=>$winner]);}

    public function dailyCheckinV2(Request $request){$uid=$this->uid($request);if(!$uid)return response()->json(['success'=>false,'message'=>'User missing'],401);$today=now()->format('Y-m-d');$old=$this->col('daily_checkins')->where('user_id',$uid)->where('date',$today)->first();if($old)return response()->json(['success'=>true,'already'=>true]);$reward=50;$this->col('daily_checkins')->insert(['user_id'=>$uid,'date'=>$today,'reward'=>$reward,'created_at'=>$this->now()]);$wallet=$this->creditCoins($uid,$reward,'daily_checkin');return response()->json(['success'=>true,'reward'=>$reward,'wallet'=>$wallet]);}
    public function referralSummary(Request $request){$uid=$this->uid($request);if(!$uid)return response()->json(['success'=>false,'message'=>'User missing'],401);return response()->json(['success'=>true,'referrals'=>$this->col('referrals')->where('referrer_id',$uid)->get()->map(fn($i)=>$this->arr($i))->values()]);}
    public function applyReferral(Request $request){$uid=$this->uid($request);if(!$uid)return response()->json(['success'=>false,'message'=>'User missing'],401);$data=$request->validate(['invite_id'=>'required|string']);$this->col('referrals')->insert(['user_id'=>$uid,'invite_id'=>$data['invite_id'],'status'=>'pending','created_at'=>$this->now()]);return response()->json(['success'=>true]);}
    public function luckySpinConfig(){ $items=$this->col('spin_prizes')->where('status','active')->get()->map(fn($i)=>$this->arr($i))->values(); if($items->isEmpty()){foreach([10,20,50,100,500] as $c)$this->col('spin_prizes')->insert(['name'=>$c.' Coins','coins'=>$c,'weight'=>10,'status'=>'active','created_at'=>$this->now()]);$items=$this->col('spin_prizes')->where('status','active')->get()->map(fn($i)=>$this->arr($i))->values();} return response()->json(['success'=>true,'prizes'=>$items]);}
    public function luckySpinPlay(Request $request){$uid=$this->uid($request);if(!$uid)return response()->json(['success'=>false,'message'=>'User missing'],401);$prize=$this->arr($this->col('spin_prizes')->where('status','active')->inRandomOrder()->first()?:['name'=>'10 Coins','coins'=>10]);$wallet=$this->creditCoins($uid,(int)($prize['coins']??0),'lucky_spin',['prize'=>$prize]);$this->col('spin_history')->insert(['user_id'=>$uid,'prize'=>$prize,'created_at'=>$this->now()]);return response()->json(['success'=>true,'prize'=>$prize,'wallet'=>$wallet]);}
    public function taskCenter(Request $request){$uid=$this->uid($request);return response()->json(['success'=>true,'tasks'=>$this->col('app_tasks')->where('status','active')->get()->map(fn($i)=>$this->arr($i))->values()]);}
    public function taskClaim(Request $request){$uid=$this->uid($request);if(!$uid)return response()->json(['success'=>false,'message'=>'User missing'],401);$data=$request->validate(['task_id'=>'required|string']);$task=$this->arr($this->col('app_tasks')->where('_id',$this->oid($data['task_id']))->first()?:[]);if(!$task)return response()->json(['success'=>false,'message'=>'Task not found'],404);$this->col('task_claims')->insert(['user_id'=>$uid,'task_id'=>$data['task_id'],'reward'=>$task['reward_coins']??0,'created_at'=>$this->now()]);$wallet=$this->creditCoins($uid,(int)($task['reward_coins']??0),'task_claim');return response()->json(['success'=>true,'wallet'=>$wallet]);}

    public function createReport(Request $request){$uid=$this->uid($request);$data=$request->validate(['target_type'=>'required|string','target_id'=>'required|string','reason'=>'required|string','description'=>'nullable|string']);$row=$data+['user_id'=>$uid,'status'=>'pending','created_at'=>$this->now()];$id=$this->col('reports')->insertGetId($row);return response()->json(['success'=>true,'report_id'=>(string)$id]);}
    public function adminReports(){return response()->json(['success'=>true,'reports'=>$this->col('reports')->orderBy('_id','desc')->limit(200)->get()->map(fn($i)=>$this->arr($i))->values()]);}
    public function adminReportAction(Request $request,$id){$data=$request->validate(['action'=>'required|string','note'=>'nullable|string']);$this->col('reports')->where('_id',$this->oid($id))->update(['status'=>$data['action'],'admin_note'=>$data['note']??null,'updated_at'=>$this->now()]);return response()->json(['success'=>true]);}
    public function adminBlacklist(Request $request){$data=$request->validate(['user_id'=>'required|string','type'=>'required|string','reason'=>'nullable|string','status'=>'nullable|string']);$data['created_at']=$this->now();$this->col('blacklist')->insert($data);$this->broadcast('user.'.$data['user_id'],'account.warning',['type'=>$data['type'],'reason'=>$data['reason']??'']);return response()->json(['success'=>true]);}

    public function familyRankingV2(){return response()->json(['success'=>true,'families'=>$this->col('families')->orderBy('points','desc')->limit(100)->get()->map(fn($i)=>$this->arr($i))->values()]);}
    public function familyLevelUp(Request $request,$id){$data=$request->validate(['level'=>'nullable|integer','points'=>'nullable|integer']);$this->col('families')->where('_id',$this->oid($id))->update(['level'=>$data['level']??1,'points'=>$data['points']??0,'updated_at'=>$this->now()]);return response()->json(['success'=>true]);}
    public function richListV2(){return response()->json(['success'=>true,'users'=>$this->col('wallet')->orderBy('coins','desc')->limit(100)->get()->map(fn($i)=>$this->arr($i))->values()]);}
    public function topHostsV2(){return response()->json(['success'=>true,'hosts'=>$this->col('hosts')->orderBy('points','desc')->limit(100)->get()->map(fn($i)=>$this->arr($i))->values()]);}

    public function adminRechargePackages(){return response()->json(['success'=>true,'items'=>$this->col('recharge_packages')->orderBy('sort_order','asc')->get()->map(fn($i)=>$this->arr($i))->values()]);}
    public function adminRechargePackageUpsert(Request $request){$data=$request->all();$data['updated_at']=$this->now();if(!empty($data['id'])){$id=$data['id'];unset($data['id']);$this->col('recharge_packages')->where('_id',$this->oid($id))->update($data);return response()->json(['success'=>true,'id'=>$id]);}$data['created_at']=$this->now();$id=$this->col('recharge_packages')->insertGetId($data);return response()->json(['success'=>true,'id'=>(string)$id]);}
    public function adminTasks(){return response()->json(['success'=>true,'items'=>$this->col('app_tasks')->orderBy('_id','desc')->get()->map(fn($i)=>$this->arr($i))->values()]);}
    public function adminTaskUpsert(Request $request){$data=$request->all();$data['updated_at']=$this->now();if(!empty($data['id'])){$id=$data['id'];unset($data['id']);$this->col('app_tasks')->where('_id',$this->oid($id))->update($data);return response()->json(['success'=>true,'id'=>$id]);}$data['created_at']=$this->now();$id=$this->col('app_tasks')->insertGetId($data);return response()->json(['success'=>true,'id'=>(string)$id]);}
    public function adminSpinPrizes(){return response()->json(['success'=>true,'items'=>$this->col('spin_prizes')->orderBy('_id','desc')->get()->map(fn($i)=>$this->arr($i))->values()]);}
    public function adminSpinPrizeUpsert(Request $request){$data=$request->all();$data['updated_at']=$this->now();if(!empty($data['id'])){$id=$data['id'];unset($data['id']);$this->col('spin_prizes')->where('_id',$this->oid($id))->update($data);return response()->json(['success'=>true,'id'=>$id]);}$data['created_at']=$this->now();$id=$this->col('spin_prizes')->insertGetId($data);return response()->json(['success'=>true,'id'=>(string)$id]);}


    // FINAL LAUNCH: Play Store, security, admin analytics and backups
    public function launchChecklist()
    {
        return response()->json(['success'=>true,'checklist'=>[
            'flutter_aab_build'=>'pending_external_flutter_sdk',
            'privacy_policy'=>'required',
            'terms_conditions'=>'required',
            'data_deletion'=>'required',
            'firebase_release_config'=>'required',
            'reverb_process'=>'required_supervisor_or_vps',
            'mongodb_backup'=>'required',
            'payment_webhook'=>'required',
            'role_permissions'=>'enabled_routes_available',
        ]]);
    }

    public function adminDashboardAnalytics()
    {
        $count = fn($c) => $this->col($c)->count();
        $sum = function($c,$field){ try { return (int)$this->col($c)->sum($field); } catch (Throwable $e) { return 0; } };
        return response()->json(['success'=>true,'analytics'=>[
            'users'=>$count('users'),
            'rooms'=>$count('rooms'),
            'active_rooms'=>$this->col('rooms')->where('status','active')->count(),
            'moments'=>$count('moments'),
            'support_open'=>$this->col('support_tickets')->where('status','open')->count(),
            'pending_withdrawals'=>$this->col('withdrawals')->where('status','pending')->count(),
            'pending_recharges'=>$this->col('recharges')->where('status','pending')->count(),
            'total_recharge_amount'=>$sum('recharges','amount'),
            'total_gift_coins'=>$sum('room_gifts','coins'),
            'wallet_coins'=>$sum('wallet','coins'),
        ]]);
    }

    public function adminActivityLogs()
    {
        return response()->json(['success'=>true,'logs'=>$this->col('activity_logs')->orderBy('_id','desc')->limit(200)->get()->map(fn($i)=>$this->arr($i))->values()]);
    }

    public function adminSecuritySettings(Request $request)
    {
        if ($request->isMethod('post')) {
            $data=$request->all();
            $data['key']='security'; $data['updated_at']=$this->now();
            $existing=$this->col('settings')->where('key','security')->first();
            if($existing) $this->col('settings')->where('key','security')->update($data);
            else $this->col('settings')->insert($data+['created_at'=>$this->now()]);
        }
        $settings=$this->arr($this->col('settings')->where('key','security')->first() ?: [
            'rate_limit_login'=>10,
            'rate_limit_api'=>120,
            'wallet_lock_enabled'=>true,
            'device_ban_enabled'=>true,
            'maintenance_mode'=>false,
        ]);
        return response()->json(['success'=>true,'settings'=>$settings]);
    }

    public function reportClientCrash(Request $request)
    {
        $data=$request->all();
        $data['user_id']=$this->uid($request);
        $data['created_at']=$this->now();
        $this->col('client_crashes')->insert($data);
        return response()->json(['success'=>true]);
    }

}
