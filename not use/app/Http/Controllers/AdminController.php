<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use MongoDB\Laravel\Facades\DB as MongoDB;

class AdminController extends Controller 
{
    // Dashboard - LIVE
    public function dashboard() 
    {
        $totalUsers = MongoDB::collection('users')->count();
        $totalHosts = MongoDB::collection('hosts')->count();
        $totalRooms = MongoDB::collection('rooms')->count();
        $totalGifts = MongoDB::collection('gifts')->count();
        
        $activeRooms = MongoDB::collection('rooms')->where('status', 'active')->count();
        $giftsSent = MongoDB::collection('gift_logs')->count();
        $reports = MongoDB::collection('reports')->count();
        
        $recentActivities = MongoDB::collection('activity_logs')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('admin.dashboard', compact(
            'totalUsers', 'totalHosts', 'totalRooms', 'totalGifts',
            'activeRooms', 'giftsSent', 'reports', 'recentActivities'
        ));
    }

    // Users
    public function users() 
    {
        $users = User::orderBy('_id', 'desc')->get();
        return view('admin.users', compact('users'));
    }
    
    public function userView($id) 
    {
        $user = MongoDB::collection('users')->where('_id', $id)->first();
        
        if(!$user) {
            return redirect()->route('admin.users')->with('error', 'User nahi mila');
        }
        
        $roomsJoined = MongoDB::collection('room_logs')->where('user_id', $user['_id'])->count();
        $roomsCreated = MongoDB::collection('rooms')->where('created_by', $user['_id'])->count();
        $giftsSent = MongoDB::collection('gift_logs')->where('sender_id', $user['_id'])->count();
        $giftsReceived = MongoDB::collection('gift_logs')->where('receiver_id', $user['_id'])->count();
        
        return view('admin.user-view', compact('user', 'roomsJoined', 'roomsCreated', 'giftsSent', 'giftsReceived'));
    }
    
    public function userDelete($id) 
    {
        User::where('_id', $id)->delete();
        return redirect()->route('admin.users')->with('success', 'User delete ho gaya');
    }

    // Rooms - LIVE
    public function rooms() 
    {
        $rooms = MongoDB::collection('rooms')->orderBy('_id', 'desc')->get();
        $totalRooms = $rooms->count();
        $activeRooms = $rooms->where('status', 'active')->count();
        
        return view('admin.rooms', compact('rooms', 'totalRooms', 'activeRooms'));
    }

    // Hosts - LIVE
    public function hosts() 
    {
        $hosts = MongoDB::collection('hosts')->orderBy('_id', 'desc')->get();
        
        $totalHosts = $hosts->count();
        $activeHosts = $hosts->where('status', 'active')->count();
        $pendingHosts = $hosts->where('status', 'pending')->count();
        $monthlyEarning = $hosts->sum('monthly_earning');
        
        return view('admin.hosts', compact('hosts', 'totalHosts', 'activeHosts', 'pendingHosts', 'monthlyEarning'));
    }
    
    public function hostView($id) 
    {
        $host = MongoDB::collection('hosts')->where('_id', $id)->first();
        return view('admin.host-view', compact('host'));
    }

    // Host Applications - PENDING WALE SIRF
    public function hostApplications()
    {
        $hosts = MongoDB::collection('hosts')
            ->where('status', 'pending')
            ->orderBy('_id', 'desc')
            ->get();
        
        return view('admin.host-applications', compact('hosts'));
    }
    
    public function hostApprove($id)
    {
        MongoDB::collection('hosts')->where('_id', $id)->update([
            'status' => 'active',
            'approved_at' => now()
        ]);
        return back()->with('success', 'Host approve ho gaya!');
    }

    public function hostReject($id)
    {
        MongoDB::collection('hosts')->where('_id', $id)->update([
            'status' => 'rejected',
            'rejected_at' => now()
        ]);
        return back()->with('success', 'Host reject kar diya!');
    }

    // Host Rankings - TOP BY DIAMONDS
    public function hostRankings()
    {
        $hosts = MongoDB::collection('hosts')
            ->where('status', 'active')
            ->orderBy('diamonds', 'desc')
            ->limit(50)
            ->get();
        
        return view('admin.host-rankings', compact('hosts'));
    }

    // Gifts - LIVE
    public function gifts() 
    {
        $gifts = MongoDB::collection('gifts')->orderBy('_id', 'desc')->get();
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
            'gift_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $imageName = null;
        if($request->hasFile('gift_image')) {
            $imageName = time() . '_' . $request->gift_image->getClientOriginalName();
            $request->gift_image->move(public_path('uploads/gifts'), $imageName);
        }

        MongoDB::collection('gifts')->insert([
            'name' => $request->gift_name,
            'price' => (int)$request->gift_price,
            'category' => $request->gift_category,
            'image' => $imageName,
            'status' => 'active',
            'created_at' => now()
        ]);

        return redirect()->route('admin.gifts')->with('success', 'Gift add ho gaya bhai!');
    }

    // Static - baad me live karenge
    public function reports() { return view('admin.reports'); }
    public function settings() { return view('admin.settings'); }
    public function withdraws() { return view('admin.withdraws'); }
    public function notifications() { return view('admin.notifications'); }
    public function agencies() { return view('admin.agencies'); }
    public function agencyCreate() { return view('admin.agency-create'); }
    public function banners() { return view('admin.banners'); }
    public function rechargePackages() { return view('admin.recharge-packages'); }
    public function vipPlans() { return view('admin.vip-plans'); }
    public function levels() { return view('admin.levels'); }
    public function tasks() { return view('admin.tasks'); }
    public function games() { return view('admin.games'); }
}