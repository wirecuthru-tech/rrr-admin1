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
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Agency ID</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($users as $key => $user)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $user->_id }}</td>
                    <td>{{ $user->name ?? 'N/A' }}</td>

                    <td>
                        <span class="badge 
                            @if($user->role == 'agency_owner') bg-primary
                            @elseif($user->role == 'host') bg-warning text-dark
                            @else bg-secondary
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $user->role ?? 'user')) }}
                        </span>
                    </td>

                    <td>
                        {{ $user->agency_id ?? '-' }}
                    </td>

                    <td>
                        <span class="badge {{ $user->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst($user->status ?? 'inactive') }}
                        </span>
                    </td>

                    <td>
                        <td>
    <td>
    <a href="{{ route('admin.user.view', $user->_id) }}" class="btn btn-info btn-sm">
        View
    </a>
    
    <form action="{{ route('admin.user.delete', $user->_id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger btn-sm" onclick="return confirm('User delete karega?')">Delete</button>
    </form>
</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Atlas me koi user nahi mila bhai 😔</td>
                </tr>
                @endforelse
            </tbody>

        </table>

    </div>
</div>

@endsection