@extends('admin.layout')

@section('page-title','Post Detail')

@section('content')

<div class="container-fluid">

    <h3>Post Detail</h3>

    <div class="card mb-3">
        <div class="card-body">
            <h5>{{ $post->badge_icon }} {{ $post->badge_name }}</h5>

            <p><b>Name:</b> {{ $post->name }}</p>
            <p><b>Phone:</b> {{ $post->phone }}</p>
            <p><b>Email:</b> {{ $post->email }}</p>
            <p><b>Country:</b> {{ $post->country }}</p>
            <p><b>Real ID:</b> {{ $post->real_id }}</p>
            <p><b>Post ID:</b> {{ $post->post_id }}</p>
            <p><b>Parent Post ID:</b> {{ $post->parent_post_id ?? '-' }}</p>
            <p><b>Status:</b> {{ $post->status }}</p>

            <hr>

            <h5>Hierarchy IDs</h5>
            @if($post->owner_post_id)<p><b>Owner:</b> {{ $post->owner_post_id }}</p>@endif
            @if($post->assistant_owner_post_id)<p><b>Assistant Owner:</b> {{ $post->assistant_owner_post_id }}</p>@endif
            @if($post->country_manager_post_id)<p><b>Country Manager:</b> {{ $post->country_manager_post_id }}</p>@endif
            @if($post->super_admin_post_id)<p><b>Super Admin:</b> {{ $post->super_admin_post_id }}</p>@endif
            @if($post->bd_post_id)<p><b>BD:</b> {{ $post->bd_post_id }}</p>@endif
            @if($post->agency_post_id)<p><b>Agency:</b> {{ $post->agency_post_id }}</p>@endif
            @if($post->host_post_id)<p><b>Host:</b> {{ $post->host_post_id }}</p>@endif

            <hr>

            <h5>Permissions</h5>
            @php $permissions = $post->permissions ?? []; @endphp

            <div class="row">
                @foreach(['add','view','edit','delete','suspend','withdrawal'] as $perm)
                    <div class="col-md-4 mb-2">
                        <b>{{ ucfirst($perm) }}:</b>
                        @if(!empty($permissions[$perm]))
                            <span class="badge bg-success">ON</span>
                        @else
                            <span class="badge bg-danger">OFF</span>
                        @endif
                    </div>
                @endforeach
            </div>

            <a href="{{ route('admin.team.tree', $post->post_id) }}" class="btn btn-primary mt-3">
                Open Team Tree
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5>All Badges For Real ID {{ $post->real_id }}</h5>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Post ID</th>
                        <th>Badge</th>
                        <th>Role</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allPosts as $item)
                        <tr>
                            <td>{{ $item->post_id }}</td>
                            <td>{{ $item->badge_icon }} {{ $item->badge_name }}</td>
                            <td>{{ $item->role }}</td>
                            <td>{{ $item->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

</div>

@endsection