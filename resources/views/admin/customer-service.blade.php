@extends('admin.layout')
@section('page-title','Customer Service')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold">Customer Service</h3>
        <div>
            <a href="{{ route('admin.customer-service', ['status'=>'all']) }}" class="btn btn-sm btn-outline-primary">All</a>
            <a href="{{ route('admin.customer-service', ['status'=>'open']) }}" class="btn btn-sm btn-outline-warning">Open</a>
            <a href="{{ route('admin.customer-service', ['status'=>'answered']) }}" class="btn btn-sm btn-outline-success">Answered</a>
            <a href="{{ route('admin.customer-service', ['status'=>'closed']) }}" class="btn btn-sm btn-outline-secondary">Closed</a>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-3"><div class="card p-3"><b>Open</b><h3>{{ $stats['open'] ?? 0 }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><b>Answered</b><h3>{{ $stats['answered'] ?? 0 }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><b>Closed</b><h3>{{ $stats['closed'] ?? 0 }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><b>Urgent</b><h3>{{ $stats['urgent'] ?? 0 }}</h3></div></div>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Ticket</th><th>User</th><th>Subject</th><th>Priority</th><th>Status</th><th>Unread</th><th>Updated</th><th></th></tr></thead>
                <tbody>
                @forelse($tickets as $t)
                    <tr>
                        <td>{{ $t['ticket_no'] ?? ($t['_id'] ?? '') }}</td>
                        <td>{{ $t['user_name'] ?? '' }}<br><small>{{ $t['real_id'] ?? $t['user_id'] ?? '' }}</small></td>
                        <td><b>{{ $t['subject'] ?? '' }}</b><br><small>{{ $t['last_message'] ?? '' }}</small></td>
                        <td><span class="badge bg-{{ ($t['priority'] ?? '') === 'urgent' ? 'danger' : 'info' }}">{{ $t['priority'] ?? 'normal' }}</span></td>
                        <td><span class="badge bg-secondary">{{ $t['status'] ?? 'open' }}</span></td>
                        <td>{{ $t['unread_admin'] ?? 0 }}</td>
                        <td>{{ $t['updated_at'] ?? '' }}</td>
                        <td><a class="btn btn-sm btn-primary" href="{{ route('admin.customer-service.view', $t['_id'] ?? '') }}">Open</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-4">No support tickets</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
