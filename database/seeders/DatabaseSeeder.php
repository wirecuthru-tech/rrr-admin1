<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $db = DB::connection('mongodb');
        $now = now();

        if ($db->table('settings')->count() === 0) {
            $db->table('settings')->insert([
                'app_name' => 'RRR Voice Chat',
                'logo_url' => '',
                'agora_app_id' => env('AGORA_APP_ID', ''),
                'agora_app_certificate' => env('AGORA_APP_CERTIFICATE', ''),
                'agora_token_expiry' => 3600,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if ($db->table('rooms')->count() === 0) {
            foreach ([['India Fun Room','music'], ['Late Night Talk','party'], ['Game Night','gaming']] as $room) {
                $db->table('rooms')->insert([
                    'title' => $room[0],
                    'theme' => $room[1],
                    'status' => 'active',
                    'currentUsers' => rand(20, 300),
                    'maxSeats' => 12,
                    'channel_name' => 'room_'.strtolower(str_replace(' ', '_', $room[0])),
                    'host_video_enabled' => true,
                    'video_rate_per_minute' => 10,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        if ($db->table('vip_plans')->count() === 0) {
            foreach ([['VIP 1',99,30], ['VIP 3',299,30], ['VIP 7',999,30]] as $plan) {
                $db->table('vip_plans')->insert(['name'=>$plan[0], 'price'=>$plan[1], 'days'=>$plan[2], 'status'=>'active', 'features'=>['badge','frame','entry_effect'], 'created_at'=>$now, 'updated_at'=>$now]);
            }
        }

        if ($db->table('gifts')->count() === 0) {
            foreach ([['Rose',10], ['Crown',100], ['Rocket',500], ['Dragon',2000]] as $gift) {
                $db->table('gifts')->insert(['name'=>$gift[0], 'price'=>$gift[1], 'status'=>'active', 'icon'=>'🎁', 'created_at'=>$now, 'updated_at'=>$now]);
            }
        }

        if ($db->table('events')->count() === 0) {
            $db->table('events')->insert(['title'=>'Singing Contest', 'description'=>'Weekly singing battle', 'status'=>'active', 'starts_at'=>$now, 'created_at'=>$now, 'updated_at'=>$now]);
        }

        if ($db->table('marketplace_items')->count() === 0) {
            $db->table('marketplace_items')->insert(['name'=>'Royal Profile Frame', 'type'=>'frame', 'price'=>199, 'status'=>'active', 'created_at'=>$now, 'updated_at'=>$now]);
        }

        if ($db->table('missions')->count() === 0) {
            foreach ([['Join 3 rooms',50], ['Send 1 gift',30], ['Daily online 30 minutes',80]] as $mission) {
                $db->table('missions')->insert(['title'=>$mission[0], 'reward'=>$mission[1], 'status'=>'active', 'created_at'=>$now, 'updated_at'=>$now]);
            }
        }
    }
}
