@extends('admin.layout')

@section('page-title', 'Rooms Management')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3>Rooms Management</h3>
        <p class="text-muted">Total: {{ $totalRooms }} | Active: {{ $activeRooms }}</p>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Room ID</th>
                    <th>Room Name</th>
                    <th>Owner</th>
                    <th>Users</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rooms as $key => $room)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td><code>{{ $room['_id'] }}</code></td>
                    <td>
                        <strong>{{ $room['name'] ?? $room['room_name'] ?? 'No Name' }}</strong>
                    </td>
                    <td>
                        @php
                            $owner = DB::collection('users')->where('_id', $room['created_by'] ?? $room['owner_id'])->first();
                        @endphp
                        {{ $owner['name'] ?? $owner['username'] ?? 'Unknown' }}
                    </td>
                    <td>
                        <span class="badge bg-info">{{ $room['user_count'] ?? $room['users'] ?? 0 }}</span>
                    </td>
                    <td>
                        <span class="badge {{ ($room['status'] ?? 'inactive') == 'active' ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst($room['status'] ?? 'inactive') }}
                        </span>
                    </td>
                    <td>
                        <small class="text-muted">{{ isset($room['created_at']) ? \Carbon\Carbon::parse($room['created_at'])->diffForHumans() : '-' }}</small>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Atlas me koi room nahi hai bhai 😔</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection