@extends('admin.layout')

@section('page-title','Team Tree')

@section('content')

<div class="container-fluid">

    <h3>{{ $post->badge_name }}</h3>

    <div class="card mb-3">
        <div class="card-body">

            <p><b>Name:</b> {{ $post->name }}</p>
            <p><b>Post ID:</b> {{ $post->post_id }}</p>
            <p><b>Real ID:</b> {{ $post->real_id }}</p>

        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Post ID</th>
                        <th>Open</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($children as $child)

                        <tr>

                            <td>{{ $child->name }}</td>
                            <td>{{ $child->badge_name }}</td>
                            <td>{{ $child->post_id }}</td>

                            <td>
                                <a href="{{ route('admin.team.tree',$child->post_id) }}"
                                   class="btn btn-success btn-sm">
                                    Open
                                </a>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="4">
                                No Team Found
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>
    </div>

</div>

@endsection