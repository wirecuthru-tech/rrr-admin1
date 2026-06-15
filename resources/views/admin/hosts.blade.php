@extends('admin.layout')

@section('page-title','Host Management')

@section('content')

<div class="d-flex justify-content-between mb-4">
    <h3>Host Management</h3>
    <button class="btn btn-primary">
        <i class="fa-solid fa-plus"></i>
        Add Host
    </button>
</div>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6>Total Hosts</h6>
                <h2>{{ $totalHosts }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6>Active Hosts</h6>
                <h2>{{ $activeHosts }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6>Pending Hosts</h6>
                <h2>{{ $pendingHosts }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6>Monthly Earnings</h6>
                <h2>₹{{ number_format($monthlyEarning) }}</h2>
            </div>
        </div>
    </div>
</div>

<!-- Host List -->
<div class="card">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
            <tr>
                <th>Host ID</th>
                <th>User ID</th>
                <th>Photo</th>
                <th>Name</th>
                <th>Agency</th>
                <th>Country</th>
                <th>Level</th>
                <th>VIP</th>
                <th>Coins</th>
                <th>Diamonds</th>
                <th>Monthly Earning</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse($hosts as $host)
            <tr>
                <td>{{ $host['host_id'] ?? $host['_id'] }}</td>
                <td>{{ $host['user_id'] ?? '-' }}</td>
                <td>
                    <img src="{{ $host['photo'] ?? 'https://via.placeholder.com/40' }}" class="rounded-circle" width="40">
                </td>
                <td>{{ $host['name'] ?? '-' }}</td>
                <td>{{ $host['agency'] ?? '-' }}</td>
                <td>{{ $host['country'] ?? '-' }}</td>
                <td>{{ $host['level'] ?? 0 }}</td>
                <td>
                    @if(!empty($host['vip_level']))
                    <span class="badge bg-warning">VIP {{ $host['vip_level'] }}</span>
                    @else
                    <span class="badge bg-secondary">VIP 0</span>
                    @endif
                </td>
                <td>{{ number_format($host['coins'] ?? 0) }}</td>
                <td>{{ number_format($host['diamonds'] ?? 0) }}</td>
                <td>₹{{ number_format($host['monthly_earning'] ?? 0) }}</td>
                <td>
                    <span class="badge bg-{{ $host['status'] == 'active' ? 'success' : 'warning' }}">
                        {{ ucfirst($host['status'] ?? 'pending') }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.host.view', $host['_id']) }}" class="btn btn-info btn-sm">
                        View
                    </a>
                    <button class="btn btn-warning btn-sm">Edit</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="13" class="text-center">No hosts found</td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection