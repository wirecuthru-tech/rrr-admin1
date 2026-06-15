@extends('admin.layout')

@section('page-title','Recharge Requests')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card shadow-sm">
        <div class="card-header fw-bold">Paytm QR / Razorpay Recharge Requests</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Gateway</th>
                        <th>Amount</th>
                        <th>Coins</th>
                        <th>Status</th>
                        <th>UTR / Payment ID</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $r)
                        <tr>
                            <td>{{ $r['user_id'] ?? $r['firebase_uid'] ?? '-' }}</td>
                            <td>{{ strtoupper($r['gateway'] ?? '-') }}</td>
                            <td>₹{{ $r['amount'] ?? 0 }}</td>
                            <td>{{ $r['coins'] ?? 0 }}</td>
                            <td><span class="badge bg-{{ ($r['status'] ?? '') === 'approved' ? 'success' : (($r['status'] ?? '') === 'rejected' ? 'danger' : 'warning') }}">{{ $r['status'] ?? 'pending' }}</span></td>
                            <td>{{ $r['utr'] ?? $r['payment_id'] ?? '-' }}</td>
                            <td>{{ $r['created_at'] ?? '-' }}</td>
                            <td>
                                @if(($r['status'] ?? 'pending') === 'pending')
                                    <form method="POST" action="{{ route('admin.recharge.requests.action', $r['_id'] ?? '') }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.recharge.requests.action', $r['_id'] ?? '') }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <button class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted">No recharge requests yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
