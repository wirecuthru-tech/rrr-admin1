@extends('admin.layout')

@section('page-title', 'Gift Management')

@section('content')

<div class="d-flex justify-content-between mb-4">
    <div>
        <h3>Gift Management</h3>
        <p class="text-muted">Manage all gifts</p>
    </div>
    <a href="{{ route('admin.gift.create') }}" class="btn btn-primary">
        + Add Gift
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Gift Name</th>
                    <th>Coins</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gifts as $key => $gift)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>
                        @if(!empty($gift['image']))
                            <img src="{{ asset('uploads/gifts/' . $gift['image']) }}" width="50" height="50" class="rounded">
                        @else
                            <img src="https://via.placeholder.com/50" width="50" height="50" class="rounded">
                        @endif
                    </td>
                    <td>{{ $gift['name'] ?? '-' }}</td>
                    <td>{{ number_format($gift['price'] ?? 0) }}</td>
                    <td>
                        <span class="badge bg-info">{{ $gift['category'] ?? 'Normal' }}</span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $gift['status'] == 'active' ? 'success' : 'danger' }}">
                            {{ ucfirst($gift['status'] ?? 'active') }}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Delete karega?')">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Koi gift nahi hai. Pehle add kar le bhai.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection