@extends('admin.layout')

@section('page-title',$country.' Team')

@section('content')

<div class="container-fluid">

    <h3>{{ $country }} Team</h3>

    <div class="card">
        <div class="card-body">

            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Post ID</th>
                        <th>Real ID</th>
                        <th>Role</th>
                        <th>Open Team</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($managers as $manager)

                        <tr>
                            <td>{{ $manager->name }}</td>
                            <td>{{ $manager->post_id }}</td>
                            <td>{{ $manager->real_id }}</td>
                            <td>{{ $manager->badge_name }}</td>

                            <td>
                                <a href="{{ route('admin.team.tree',$manager->post_id) }}"
                                   class="btn btn-primary btn-sm">
                                    Open
                                </a>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="5">
                                No Country Manager Found
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>
    </div>

</div>

@endsection