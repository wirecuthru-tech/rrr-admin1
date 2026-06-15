@extends('admin.layout')

@section('page-title','Country Teams')

@section('content')

<div class="container-fluid">

    <h3>Country Teams</h3>

    <div class="card">
        <div class="card-body table-responsive">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Country</th>
                        <th>Total Posts</th>
                        <th>Country Managers</th>
                        <th>Super Admins</th>
                        <th>BD</th>
                        <th>Agencies</th>
                        <th>Hosts</th>
                        <th>Open</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($countries as $item)
                        <tr>
                            <td>{{ $item['country'] }}</td>
                            <td>{{ $item['total_posts'] }}</td>
                            <td>{{ $item['country_managers'] }}</td>
                            <td>{{ $item['super_admins'] }}</td>
                            <td>{{ $item['bds'] }}</td>
                            <td>{{ $item['agencies'] }}</td>
                            <td>{{ $item['hosts'] }}</td>
                            <td>
                                <a href="{{ route('admin.country.view', $item['country']) }}" class="btn btn-primary btn-sm">
                                    Open
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8">No country team found</td></tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

</div>

@endsection