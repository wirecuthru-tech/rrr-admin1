@extends('admin.layout')

@section('page-title', 'User Management')

@section('content')

<div class="card">
<div class="card-body">

<h3 class="mb-4">Users Management</h3>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered table-hover">
<thead class="table-dark">
<tr>
    <th>#</th>
    <th>Real ID</th>
    <th>Name</th>
    <th>Role</th>
    <th>Agency ID</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
@forelse($users as $key => $user)

@php
    $realId = $user['realId'] ?? $user['owner_id'] ?? null;
    $mongoId = $user['_id'] ?? $user['id'] ?? null;

    if (is_array($mongoId) && isset($mongoId['oid'])) {
        $mongoId = $mongoId['oid'];
    }

    $routeId = $realId ?: $mongoId;
@endphp

<tr>
    <td>{{ $key + 1 }}</td>

    <td>{{ $realId ?? 'N/A' }}</td>

    <td>{{ $user['name'] ?? 'N/A' }}</td>

    <td>
        <span class="badge
            @if(($user['role'] ?? '') == 'agency_owner') bg-primary
            @elseif(($user['role'] ?? '') == 'host') bg-warning text-dark
            @elseif(($user['role'] ?? '') == 'owner') bg-success
            @else bg-secondary
            @endif">
            {{ ucfirst(str_replace('_', ' ', $user['role'] ?? 'user')) }}
        </span>
    </td>

    <td>{{ $user['agency_id'] ?? '-' }}</td>

    <td>
        <span class="badge {{ (($user['status'] ?? '') == 'active' || ($user['is_active'] ?? false)) ? 'bg-success' : 'bg-danger' }}">
            {{ (($user['status'] ?? '') == 'active' || ($user['is_active'] ?? false)) ? 'Active' : 'Inactive' }}
        </span>
    </td>

    <td>
        @if(!empty($routeId))
            <a href="{{ route('admin.user.view', ['id' => $routeId]) }}" class="btn btn-info btn-sm">
                View
            </a>

            <form action="{{ route('admin.user.delete', ['id' => $routeId]) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm" onclick="return confirm('User delete karega?')">
                    Delete
                </button>
            </form>
        @else
            <span class="badge bg-secondary">No ID</span>
        @endif
    </td>
</tr>

@empty
<tr>
    <td colspan="7" class="text-center">Atlas me koi user nahi mila</td>
</tr>
@endforelse
</tbody>
</table>

</div>
</div>

@endsection