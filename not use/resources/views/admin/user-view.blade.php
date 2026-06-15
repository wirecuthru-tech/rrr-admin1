@extends('admin.layout')

@section('page-title', 'User Profile')

@section('content')

<div class="row">
    <!-- Profile Card -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <img src="{{ $user['avatar'] ?? $user['photo'] ?? 'https://via.placeholder.com/150' }}" 
                     class="rounded-circle mb-3" width="150" height="150">

                <h4>{{ $user['name'] ?? 'N/A' }}</h4>
                <p class="text-muted">User ID : {{ $user['user_id'] ?? $user['_id'] }}</p>

                @if(!empty($user['role']))
                <span class="badge bg-primary">{{ strtoupper($user['role']) }}</span>
                <br><br>
                @endif

                <span class="badge bg-{{ $user['status'] == 'active' ? 'success' : 'danger' }}">
                    {{ ucfirst($user['status'] ?? 'active') }}
                </span>
            </div>
        </div>
    </div>

    <!-- User Details -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-4">User Information</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Name</strong><br>
                        {{ $user['name'] ?? '-' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Username</strong><br>
                        {{ $user['username'] ?? '-' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Gender</strong><br>
                        {{ $user['gender'] ?? '-' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Country</strong><br>
                        {{ $user['country'] ?? '-' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Mobile</strong><br>
                        {{ $user['mobile'] ?? $user['phone'] ?? '-' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Email</strong><br>
                        {{ $user['email'] ?? '-' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Role</strong><br>
                        {{ $user['role'] ?? '-' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Agency ID</strong><br>
                        {{ $user['agency_id'] ?? '-' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Agency Name</strong><br>
                        {{ $user['agency_name'] ?? '-' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>VIP Status</strong><br>
                        VIP {{ $user['vip_level'] ?? 0 }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Total Coins</strong><br>
                        {{ number_format($user['coins'] ?? 0) }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Total Diamonds</strong><br>
                        {{ number_format($user['diamonds'] ?? 0) }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Referral Code</strong><br>
                        {{ $user['referral_code'] ?? '-' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Device Type</strong><br>
                        {{ $user['device_type'] ?? '-' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Device Model</strong><br>
                        {{ $user['device_model'] ?? '-' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Registration IP</strong><br>
                        {{ $user['reg_ip'] ?? '-' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Last Login IP</strong><br>
                        {{ $user['last_ip'] ?? '-' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Join Date</strong><br>
                        {{ isset($user['created_at']) ? date('d-m-Y', strtotime($user['created_at'])) : '-' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Last Login</strong><br>
                        {{ isset($user['last_login']) ? date('d-m-Y h:i A', strtotime($user['last_login'])) : '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h6>Rooms Joined</h6>
                <h2>{{ $roomsJoined }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h6>Rooms Created</h6>
                <h2>{{ $roomsCreated }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h6>Gifts Sent</h6>
                <h2>{{ $giftsSent }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h6>Gifts Received</h6>
                <h2>{{ $giftsReceived }}</h2>
            </div>
        </div>
    </div>
</div>

<!-- Wallet Information -->
<div class="card mt-4">
    <div class="card-body">
        <h4 class="mb-4">Wallet Information</h4>
        <div class="row">
            <div class="col-md-4">
                <div class="alert alert-warning">
                    <strong>Coins</strong><br>
                    {{ number_format($user['coins'] ?? 0) }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-info">
                    <strong>Diamonds</strong><br>
                    {{ number_format($user['diamonds'] ?? 0) }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-success">
                    <strong>Total Earnings</strong><br>
                    ₹{{ number_format($user['total_earnings'] ?? 0) }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actions -->
<div class="mt-4">
    <button class="btn btn-primary">Edit User</button>
    <button class="btn btn-warning">Ban User</button>
    <button class="btn btn-success">Wallet History</button>
    <button class="btn btn-info">Login History</button>
    <a href="{{ route('admin.user.delete', $user['_id']) }}" class="btn btn-danger" onclick="return confirm('Delete karna hai?')">Delete User</a>
</div>

@endsection