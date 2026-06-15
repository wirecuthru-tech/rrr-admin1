@extends('admin.layout')

@section('page-title','Video Calls')

@section('content')

<div class="card"><div class="card-body table-responsive">
<table class="table table-bordered"><thead class="table-dark"><tr><th>Caller</th><th>Host</th><th>Room</th><th>Status</th><th>Rate/min</th><th>Duration</th><th>Created</th></tr></thead><tbody>
@forelse($calls as $c)<tr><td>{{ $c['caller_id'] ?? '-' }}</td><td>{{ $c['host_id'] ?? '-' }}</td><td>{{ $c['room_id'] ?? '-' }}</td><td>{{ $c['status'] ?? '-' }}</td><td>{{ $c['rate_per_minute'] ?? 0 }}</td><td>{{ $c['duration_seconds'] ?? 0 }} sec</td><td>{{ $c['created_at'] ?? '-' }}</td></tr>@empty<tr><td colspan="7" class="text-center">No calls</td></tr>@endforelse
</tbody></table></div></div>

@endsection
