@extends('admin.layout')

@section('page-title','Host Applications')

@section('content')

<div class="d-flex justify-content-between mb-4">
    <div>
        <h3>Host Applications</h3>
        <p class="text-muted">Pending host requests</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
<div class="card-body">
<table class="table table-bordered table-hover align-middle">
<thead class="table-dark">
<tr>
<th>#</th>
<th>User ID</th>
<th>Name</th>
<th>Country</th>
<th>Agency</th>
<th>Date</th>
<th>Action</th>
</tr>
</thead>
<tbody>
@forelse($hosts as $key => $host)
<tr>
<td>{{ $key + 1 }}</td>
<td>{{ $host['user_id'] ?? $host['_id'] }}</td>
<td>{{ $host['name'] ?? $host['username'] ?? '-' }}</td>
<td>{{ $host['country'] ?? '-' }}</td>
<td><span class="badge bg-info">{{ $host['agency_id'] ?? $host['agency'] ?? 'None' }}</span></td>
<td>
@if(!empty($host['created_at']))
{{ date('d-m-Y', strtotime($host['created_at'])) }}
@endif
</td>
<td>
<form action="{{ route('admin.host.approve', $host['_id']) }}" method="POST" class="d-inline">
@csrf
<button class="btn btn-success btn-sm">Approve</button>
</form>
<form action="{{ route('admin.host.reject', $host['_id']) }}" method="POST" class="d-inline">
@csrf
<button class="btn btn-danger btn-sm" onclick="return confirm('Reject karega?')">Reject</button>
</form>
<a href="{{ route('admin.host.view', $host['_id']) }}" class="btn btn-primary btn-sm">View</a>
</td>
</tr>
@empty
<tr>
<td colspan="7" class="text-center text-muted">Koi pending application nahi hai</td>
</tr>
@endforelse
</tbody>
</table>
</div>
</div>
@endsection