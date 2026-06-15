@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <h2 class="mb-3">Host 7 Day Task System</h2>
    <p class="text-muted">Verified host ko 7 din ka free task: har din 2 hours live + 5 calls. Daily task complete hone par 5000 coins reward eligible. 7 din me 115000 target complete hua to auto withdrawal request create hogi, warna withdrawal demand 0 ho jayegi.</p>

    <div class="row mb-3">
        <div class="col-md-3"><div class="card p-3"><b>Active</b><h3>{{ $stats['active'] ?? 0 }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><b>Completed</b><h3>{{ $stats['completed'] ?? 0 }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><b>Failed Target</b><h3>{{ $stats['failed_target'] ?? 0 }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><b>Auto Withdrawals</b><h3>{{ $stats['auto_withdrawals'] ?? 0 }}</h3></div></div>
    </div>

    <div class="mb-3">
        <a class="btn btn-sm btn-secondary" href="{{ route('admin.host-task-system', ['status'=>'all']) }}">All</a>
        <a class="btn btn-sm btn-primary" href="{{ route('admin.host-task-system', ['status'=>'active']) }}">Active</a>
        <a class="btn btn-sm btn-success" href="{{ route('admin.host-task-system', ['status'=>'completed']) }}">Completed</a>
        <a class="btn btn-sm btn-danger" href="{{ route('admin.host-task-system', ['status'=>'failed_target']) }}">Failed Target</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead><tr>
                    <th>Host</th><th>Real ID</th><th>Status</th><th>Live</th><th>Calls</th><th>Target</th><th>Reward</th><th>Withdraw</th><th>Action</th>
                </tr></thead>
                <tbody>
                @forelse($tasks as $task)
                    <tr>
                        <td>{{ $task['name'] ?? 'RRR Host' }}</td>
                        <td>{{ $task['real_id'] ?? '-' }}</td>
                        <td><span class="badge bg-dark">{{ $task['status'] ?? 'active' }}</span></td>
                        <td>{{ $task['total_minutes'] ?? 0 }}/840 min</td>
                        <td>{{ $task['total_calls'] ?? 0 }}/35</td>
                        <td>{{ $task['total_earnings'] ?? 0 }}/{{ $task['target_amount'] ?? 115000 }}</td>
                        <td>{{ $task['reward_status'] ?? 'pending' }} / {{ $task['reward'] ?? 5000 }}</td>
                        <td>{{ $task['withdrawal_demand'] ?? 0 }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.host-task-system.settle', $task['_id'] ?? $task['id']) }}">
                                @csrf
                                <button class="btn btn-sm btn-warning" type="submit">Settle</button>
                            </form>
                        </td>
                    </tr>
                    <tr><td colspan="9">
                        @foreach(($task['days'] ?? []) as $day)
                            <span class="badge {{ ($day['completed'] ?? false) ? 'bg-success' : 'bg-secondary' }} me-1">
                                D{{ $day['day'] ?? '-' }}: {{ $day['completed_minutes'] ?? 0 }}/120m, {{ $day['completed_calls'] ?? 0 }}/5 calls
                            </span>
                        @endforeach
                    </td></tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted p-4">No host task records yet</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
