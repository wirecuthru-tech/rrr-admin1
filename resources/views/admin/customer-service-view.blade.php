@extends('admin.layout')
@section('page-title','Support Ticket')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h3 class="fw-bold">{{ $ticket['subject'] ?? 'Support Ticket' }}</h3>
            <div class="text-muted">{{ $ticket['ticket_no'] ?? $id }} • {{ $ticket['user_name'] ?? '' }} • {{ $ticket['real_id'] ?? '' }}</div>
        </div>
        <form method="POST" action="{{ route('admin.customer-service.status', $id) }}" class="d-flex gap-2 align-items-start">
            @csrf
            <select name="status" class="form-select">
                @foreach(['open','answered','closed'] as $s)
                    <option value="{{ $s }}" @selected(($ticket['status'] ?? '') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button class="btn btn-secondary">Update</button>
        </form>
    </div>
    <div class="card mb-3 p-3" style="max-height: 520px; overflow:auto; background:#fff7fb;">
        @forelse($messages as $m)
            @php($isAdmin = ($m['sender_type'] ?? '') === 'admin')
            <div class="d-flex mb-2 {{ $isAdmin ? 'justify-content-end' : 'justify-content-start' }}">
                <div class="p-3 rounded shadow-sm" style="max-width:70%; background:{{ $isAdmin ? '#ffe1ee' : '#ffffff' }};">
                    <div class="fw-bold small text-{{ $isAdmin ? 'danger' : 'primary' }}">{{ $isAdmin ? 'Customer Service' : 'User' }}</div>
                    <div>{{ $m['message'] ?? '' }}</div>
                    <div class="small text-muted mt-1">{{ $m['created_at'] ?? '' }}</div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted">No messages</div>
        @endforelse
    </div>
    <div class="card p-3">
        <form method="POST" action="{{ route('admin.customer-service.reply', $id) }}">
            @csrf
            <label class="form-label fw-bold">Reply to user</label>
            <textarea name="message" class="form-control" rows="4" required placeholder="User ko reply likho..."></textarea>
            <button class="btn btn-primary mt-3">Send Reply</button>
        </form>
    </div>
</div>
@endsection
