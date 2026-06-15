<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RRR Chat Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            background:#f4f7fe;
            font-family:Arial,sans-serif;
        }

        .sidebar{
            width:260px;
            height:100vh;
            position:fixed;
            left:0;
            top:0;
            overflow-y:auto;
            background:linear-gradient(180deg,#1e3a8a 0%,#111827 100%);
        }

        .logo{
            color:#fff;
            text-align:center;
            font-size:22px;
            font-weight:bold;
            padding:25px;
            border-bottom:1px solid rgba(255,255,255,.1);
        }

        .menu-link{
            display:flex;
            justify-content:space-between;
            align-items:center;
            color:#cbd5e1;
            text-decoration:none;
            padding:14px 20px;
            transition:.3s;
        }

        .menu-link:hover{
            background:#1f2937;
            color:#fff;
        }

        .submenu a{
            display:block;
            padding:10px 45px;
            color:#cbd5e1;
            text-decoration:none;
        }

        .submenu a:hover{
            background:#374151;
            color:#fff;
        }

        .main{
            margin-left:260px;
            min-height:100vh;
        }

        .topbar{
            background:#fff;
            padding:15px 25px;
            box-shadow:0 2px 10px rgba(0,0,0,.05);
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        .content{
            padding:25px;
        }

        .user-box{
            display:flex;
            align-items:center;
            gap:10px;
        }

        .user-avatar{
            width:40px;
            height:40px;
            border-radius:50%;
            background:#2563eb;
            color:#fff;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:bold;
        }

    </style>

</head>
<body>

<div class="sidebar">

    <div class="logo">
        <i class="fa-solid fa-microphone"></i>
        RRR CHAT ADMIN
    </div>

    <a href="/admin" class="menu-link">
        <span>
            <i class="fa-solid fa-gauge"></i>
            Dashboard
        </span>
    </a>

    <div id="sidebarAccordion">

        <!-- User -->

        <a class="menu-link"
           data-bs-toggle="collapse"
           href="#userMenu">

            <span>
                <i class="fa-solid fa-users"></i>
                User Management
            </span>

            <i class="fa-solid fa-chevron-down"></i>

        </a>

        <div id="userMenu"
             class="collapse"
             data-bs-parent="#sidebarAccordion">

            <div class="submenu">
                <a href="/admin/users">User List</a>
                <a href="/admin/agencies">Agencies</a>
            </div>

        </div>

        <!-- Host -->

        <a class="menu-link"
           data-bs-toggle="collapse"
           href="#hostMenu">

            <span>
                <i class="fa-solid fa-headset"></i>
                Host Management
            </span>

            <i class="fa-solid fa-chevron-down"></i>

        </a>

        <div id="hostMenu"
             class="collapse"
             data-bs-parent="#sidebarAccordion">

            <div class="submenu">
                <a href="/admin/hosts">Host List</a>
                <a href="/admin/host-applications">Applications</a>
                <a href="/admin/host-salaries">Salaries</a>
                <a href="/admin/host-withdraws">Withdraws</a>
                <a href="/admin/host-rankings">Rankings</a>
            </div>

        </div>

        <!-- Room -->

        <a class="menu-link"
           data-bs-toggle="collapse"
           href="#roomMenu">

            <span>
                <i class="fa-solid fa-video"></i>
                Room Management
            </span>

            <i class="fa-solid fa-chevron-down"></i>

        </a>

        <div id="roomMenu"
             class="collapse"
             data-bs-parent="#sidebarAccordion">

            <div class="submenu">
                <a href="/admin/rooms">Rooms</a>
                <a href="/admin/gifts">Gifts</a>
                <a href="/admin/reports">Reports</a>
            </div>

        </div>

        <!-- Finance -->

        <a class="menu-link"
           data-bs-toggle="collapse"
           href="#financeMenu">

            <span>
                <i class="fa-solid fa-money-bill-wave"></i>
                Finance
            </span>

            <i class="fa-solid fa-chevron-down"></i>

        </a>

        <div id="financeMenu"
             class="collapse"
             data-bs-parent="#sidebarAccordion">

            <div class="submenu">
                <a href="/admin/recharge-packages">Recharge Packages</a>
                <a href="/admin/withdraws">Withdraw Requests</a>
                <a href="/admin/vip-plans">VIP Plans</a>
            </div>

        </div>

        <a href="/admin/settings" class="menu-link">
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

            <div class="user-avatar">
                A
            </div>

            <strong>Admin</strong>

        </div>

    </div>

    <div class="content">
        @yield('content')
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>