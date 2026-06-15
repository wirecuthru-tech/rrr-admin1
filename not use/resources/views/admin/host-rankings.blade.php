@extends('admin.layout')

@section('page-title','Host Rankings')

@section('content')

<div class="d-flex justify-content-between mb-4">
    <div>
        <h3>Host Rankings</h3>
        <p class="text-muted">Top 50 hosts by diamonds</p>
    </div>
</div>

<div class="card">
<div class="card-body">
<table class="table table-hover align-middle">
<thead class="table-dark">
<tr>
<th>Rank</th>
<th>Host</th>
<th>Agency</th>
<th>Diamonds</th>
<th>Status</th>
</tr>
</thead>
<tbody>
@forelse($hosts as $key => $host)
<tr>
<td>
    @if($key == 0)
        <span class="badge bg-warning">🥇 #1</span>
    @elseif($key == 1)
        <span class="badge bg-secondary">🥈 #2</span>
    @elseif($key == 2)
        <span class="badge bg-danger">🥉 #3</span>
    @else
        #{{ $key + 1 }}
    @endif
</td>
<td>
    <div class="d-flex align-items-center">
        @if(!empty($host['avatar']))
            <img src="{{ asset('uploads/avatars/' . $host['avatar']) }}" width="40" height="40" class="rounded-circle me-2">
        @else
            <img src="https://ui-avatars.com/api/?name={{ urlencode($host['name'] ?? 'H') }}" width="40" height="40" class="rounded-circle me-2">
        @endif
        <span>{{ $host['name'] ?? $host['username'] ?? '-' }}</span>
    </div>
</td>
<td>
    <span class="badge bg-info">{{ $host['agency_name'] ?? $host['agency'] ?? 'No Agency' }}</span>
</td>
<td>
    <span class="fw-bold text-success">{{ number_format($host['diamonds'] ?? 0) }}</span>
</td>
<td>
    <span class="badge bg-success">{{ ucfirst($host['status'] ?? 'active') }}</span>
</td>
</tr>
@empty
<tr>
<td colspan="5" class="text-center text-muted">Koi active host nahi hai</td>
</tr>
@endforelse
</tbody>
</table>
</div>
</div>

@endsection