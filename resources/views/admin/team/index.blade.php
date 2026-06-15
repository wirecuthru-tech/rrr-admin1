@extends('admin.layout')

@section('page-title',$roleName.' List')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between mb-3">
        <h3>{{ $roleName }} List</h3>

        <a href="{{ route('admin.team.create', $role) }}" class="btn btn-primary">
            Add {{ $roleName }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Post ID</th>
                        <th>Real ID</th>
                        <th>Badge</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Country</th>
                        <th>Status</th>
                        <th>Primary</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                        <tr>
                            <td>{{ $post->post_id }}</td>
                            <td>{{ $post->real_id }}</td>
                            <td>{{ $post->badge_icon }} {{ $post->badge_name }}</td>
                            <td>{{ $post->name }}</td>
                            <td>{{ $post->phone }}</td>
                            <td>{{ $post->country }}</td>
                            <td>{{ $post->status }}</td>
                            <td>
                                @if($post->is_primary)
                                    <span class="badge bg-success">YES</span>
                                @else
                                    <span class="badge bg-secondary">NO</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.team.show', $post->_id) }}" class="btn btn-info btn-sm">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9">No data found</td></tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>
</div>

@endsection