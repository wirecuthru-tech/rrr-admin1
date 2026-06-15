<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RRR Chat Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        *{margin:0;padding:0;box-sizing:border-box;}

        body{
            background:#f3e8ff !important;
            font-family:Arial,sans-serif;
        }

        .sidebar{
            width:260px;
            height:100vh;
            position:fixed;
            left:0;
            top:0;
            overflow-y:auto;
            background:linear-gradient(180deg,#8b5cf6 0%,#4c1d95 100%) !important;
            box-shadow:5px 0 25px rgba(124,58,237,.35);
        }

        .logo{
            color:#fff;
            text-align:center;
            font-size:22px;
            font-weight:bold;
            padding:25px;
            border-bottom:1px solid rgba(255,255,255,.2);
            text-shadow:0 0 14px rgba(255,255,255,.8);
        }

        .menu-link{
            display:flex;
            justify-content:space-between;
            align-items:center;
            color:#fff;
            text-decoration:none;
            padding:14px 20px;
            margin:6px 10px;
            border-radius:14px;
            transition:.3s;
        }

        .menu-link:hover{
            background:rgba(255,255,255,.18);
            color:#fff;
            box-shadow:0 0 18px rgba(255,255,255,.6);
            transform:translateX(4px);
        }

        .submenu a{
            display:block;
            padding:10px 45px;
            margin:4px 12px;
            color:#f5e8ff;
            text-decoration:none;
            border-radius:12px;
            transition:.3s;
        }

        .submenu a:hover{
            background:rgba(255,255,255,.18);
            color:#fff;
            box-shadow:0 0 14px rgba(255,255,255,.5);
        }

        .main{
            margin-left:260px;
            min-height:100vh;
        }

        .topbar{
            background:#fff !important;
            padding:15px 25px;
            box-shadow:0 4px 18px rgba(124,58,237,.15);
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        .topbar h5{
            color:#4c1d95;
            font-weight:700;
        }

        .content{
            padding:25px;
        }

        .card{
            border:none !important;
            border-radius:22px !important;
            box-shadow:0 12px 30px rgba(124,58,237,.18) !important;
        }

        .btn{
            border-radius:12px !important;
            transition:.25s;
        }

        .btn:hover,
        .btn:active{
            box-shadow:0 0 18px rgba(192,132,252,.9);
            transform:translateY(-1px);
        }

        .btn-primary{
            background:#8b5cf6 !important;
            border-color:#8b5cf6 !important;
        }

        .btn-primary:hover{
            background:#7c3aed !important;
            border-color:#7c3aed !important;
        }

        .table-dark th{
            background:#7c3aed !important;
            border-color:#7c3aed !important;
        }

        .user-box{
            display:flex;
            align-items:center;
            gap:10px;
            color:#4c1d95;
        }

        .user-avatar{
            width:42px;
            height:42px;
            border-radius:50%;
            background:linear-gradient(135deg,#8b5cf6,#c084fc);
            color:#fff;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:bold;
            box-shadow:0 0 18px rgba(192,132,252,.9);
        }
    </style>
</head>

<body>

<div class="sidebar">

    <div class="logo">
        <i class="fa-solid fa-microphone"></i>
        RRR CHAT ADMIN
    </div>

    <a href="{{ route('admin.dashboard') }}" class="menu-link">
        <span>
            <i class="fa-solid fa-gauge"></i>
            Dashboard
        </span>
    </a>

    <div id="sidebarAccordion">

        <a class="menu-link" data-bs-toggle="collapse" href="#teamMenu">
            <span>
                <i class="fa-solid fa-sitemap"></i>
                Team Hierarchy
            </span>
            <i class="fa-solid fa-chevron-down"></i>
        </a>

        <div id="teamMenu" class="collapse" data-bs-parent="#sidebarAccordion">
            <div class="submenu">
                <a href="{{ route('admin.team.index', 'assistant_owner') }}">Assistant Owners</a>
                <a href="{{ route('admin.team.index', 'country_manager') }}">Country Managers</a>
                <a href="{{ route('admin.team.index', 'super_admin') }}">Super Admins</a>
                <a href="{{ route('admin.team.index', 'bd') }}">BD</a>
                <a href="{{ route('admin.team.index', 'agency') }}">Agencies</a>
                <a href="{{ route('admin.team.index', 'host') }}">Hosts</a>

<a href="{{ route('admin.country.teams') }}">Country Teams</a>
<a href="{{ route('admin.team.index', 'assistant_owner') }}">Assistant Owners</a>
<a href="{{ route('admin.team.index', 'country_manager') }}">Country Managers</a>
<a href="{{ route('admin.team.index', 'super_admin') }}">Super Admins</a>
<a href="{{ route('admin.team.index', 'bd') }}">BD</a>
<a href="{{ route('admin.team.index', 'agency') }}">Agencies</a>
<a href="{{ route('admin.team.index', 'host') }}">Hosts</a>







            </div>

        </div>

        <a class="menu-link" data-bs-toggle="collapse" href="#userMenu">
            <span>
                <i class="fa-solid fa-users"></i>
                User Management
            </span>
            <i class="fa-solid fa-chevron-down"></i>
        </a>

        <div id="userMenu" class="collapse" data-bs-parent="#sidebarAccordion">
            <div class="submenu">
                <a href="{{ route('admin.users') }}">User List</a>
                <a href="{{ route('admin.verification.center') }}">Verification Center</a>
                <a href="{{ route('admin.verifications.users') }}">User Verification</a>
                <a href="{{ route('admin.verifications.hosts') }}">Host Verification</a>
                <a href="{{ route('admin.agencies') }}">Agencies</a>
            </div>
        </div>

        <a class="menu-link" data-bs-toggle="collapse" href="#hostMenu">
            <span>
                <i class="fa-solid fa-headset"></i>
                Host Management
            </span>
            <i class="fa-solid fa-chevron-down"></i>
        </a>

        <div id="hostMenu" class="collapse" data-bs-parent="#sidebarAccordion">
            <div class="submenu">
                <a href="{{ route('admin.hosts') }}">Host List</a>
                <a href="{{ route('admin.host-applications') }}">Applications</a>
                <a href="{{ route('admin.host-salaries') }}">Salaries</a>
                <a href="{{ route('admin.host-withdraws') }}">Withdraws</a>
                <a href="{{ route('admin.host-task-system') }}">7 Day Task System</a>
                <a href="{{ route('admin.host-rankings') }}">Rankings</a>
            </div>
        </div>

        <a class="menu-link" data-bs-toggle="collapse" href="#roomMenu">
            <span>
                <i class="fa-solid fa-video"></i>
                Room Management
            </span>
            <i class="fa-solid fa-chevron-down"></i>
        </a>

        <div id="roomMenu" class="collapse" data-bs-parent="#sidebarAccordion">
            <div class="submenu">
                <a href="{{ route('admin.rooms') }}">Rooms</a>
                <a href="{{ route('admin.gifts') }}">Gifts</a>
                <a href="{{ route('admin.reports') }}">Reports</a>
            </div>
        </div>

        <a class="menu-link" data-bs-toggle="collapse" href="#financeMenu">
            <span>
                <i class="fa-solid fa-money-bill-wave"></i>
                Finance
            </span>
            <i class="fa-solid fa-chevron-down"></i>
        </a>

        <div id="financeMenu" class="collapse" data-bs-parent="#sidebarAccordion">
            <div class="submenu">
                <a href="{{ route('admin.recharge-packages') }}">Recharge Packages</a>
                <a href="{{ route('admin.recharge.requests') }}">Recharge Requests</a>
                <a href="{{ route('admin.coin-sellers') }}">Coin Seller Center</a>
                <a href="{{ route('admin.payment.settings') }}">Payment Settings</a>
                <a href="{{ route('admin.withdraws') }}">Withdraw Requests</a>
                <a href="{{ route('admin.vip-plans') }}">VIP Plans</a>
            </div>
        </div>

        
        <a class="menu-link" data-bs-toggle="collapse" href="#appLiveMenu">
            <span><i class="fa-solid fa-mobile-screen"></i> App Live Modules</span>
            <i class="fa-solid fa-chevron-down"></i>
        </a>
        <div id="appLiveMenu" class="collapse" data-bs-parent="#sidebarAccordion">
            <div class="submenu">
                <a href="{{ route('admin.agora.settings') }}">Agora Settings</a>
                <a href="{{ route('admin.video.calls') }}">Video Calls</a>
                <a href="{{ route('admin.moments') }}">Moments</a>
                <a href="{{ route('admin.families') }}">Families</a>
                <a href="{{ route('admin.pk.battles') }}">PK Battles</a>
                <a href="{{ route('admin.events') }}">Events</a>
                <a href="{{ route('admin.customer-service') }}">Customer Service</a>

                <a href="{{ route('admin.rankings.live') }}">Rankings</a>
                <a href="{{ route('admin.family.chat') }}">Family Chat</a>
                <a href="{{ route('admin.voice.reels') }}">Voice Reels</a>
                <a href="{{ route('admin.stories') }}">Stories</a>
                <a href="{{ route('admin.podcasts') }}">Podcasts</a>
                <a href="{{ route('admin.country.war') }}">Country War</a>
                <a href="{{ route('admin.marketplace') }}">Marketplace</a>
                <a href="{{ route('admin.creator.shop') }}">Creator Shop</a>
                <a href="{{ route('admin.missions') }}">Missions</a>
            </div>
        </div>

        <a href="{{ route('admin.settings') }}" class="menu-link">
            <span>
                <i class="fa-solid fa-gear"></i>
                Settings
            </span>
        </a>

    </div>

</div>

<div class="main">

    <div class="topbar">
        <h5>
            @yield('page-title','RRR Chat Admin')
        </h5>

        <div class="user-box">
            <div class="user-avatar">A</div>
            <strong>{{ session('admin_name', 'Admin') }}</strong>
        </div>
    </div>

    <div class="content">
        @yield('content')
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>