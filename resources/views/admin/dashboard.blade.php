@extends('admin.layout')

@section('page-title', 'Dashboard')

@section('content')

<h3 class="mb-4">Dashboard Overview</h3>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="stat-card bg-primary text-white p-4 rounded">
            <div class="stat-title">Total Users</div>
            <div class="stat-value fs-2 fw-bold">{{ number_format($totalUsers ?? 0) }}</div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stat-card bg-success text-white p-4 rounded">
            <div class="stat-title">Active Rooms</div>
            <div class="stat-value fs-2 fw-bold">{{ number_format($activeRooms ?? 0) }}</div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stat-card bg-warning text-white p-4 rounded">
            <div class="stat-title">Gifts Sent</div>
            <div class="stat-value fs-2 fw-bold">{{ number_format($giftsSent ?? 0) }}</div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stat-card bg-danger text-white p-4 rounded">
            <div class="stat-title">Reports</div>
            <div class="stat-value fs-2 fw-bold">{{ number_format($reports ?? 0) }}</div>
        </div>
    </div>
</div>

<div class="card-box">
    <h5 class="mb-3">Recent Activity</h5>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>User</th>
                <th>Action</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentActivities as $activity)
            <tr>
                <td>{{ $activity['user_name'] ?? $activity['user'] ?? 'N/A' }}</td>
                <td>{{ $activity['action'] ?? $activity['type'] ?? 'N/A' }}</td>
                <td>
                    @if(!empty($activity['created_at']))
                        {{ date('d-m-Y h:i A', strtotime($activity['created_at'])) }}
                    @else
                        N/A
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center text-muted">Abhi koi activity nahi hai</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection