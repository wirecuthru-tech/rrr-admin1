@extends('admin.layout')

@section('page-title','Agency Details')

@section('content')

<div class="card">
    <div class="card-body">

        <h3 class="mb-4">Agency Details</h3>

        <table class="table table-bordered">

            <tr>
                <th width="250">Agency Name</th>
                <td>{{ $agency->agency_name }}</td>
            </tr>

            <tr>
                <th>Owner Name</th>
                <td>{{ $agency->owner_name }}</td>
            </tr>

            <tr>
                <th>Mobile</th>
                <td>{{ $agency->mobile }}</td>
            </tr>

            <tr>
                <th>Email</th>
                <td>{{ $agency->email }}</td>
            </tr>

            <tr>
                <th>Commission</th>
                <td>{{ $agency->commission }}%</td>
            </tr>

            <tr>
                <th>Country</th>
                <td>{{ $agency->country }}</td>
            </tr>

            <tr>
                <th>Status</th>
                <td>
                    @if($agency->status=='Active')
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </td>
            </tr>

            <tr>
                <th>Wallet Balance</th>
                <td>? {{ $agency->wallet ?? 0 }}</td>
            </tr>

            <tr>
                <th>Created Date</th>
                <td>{{ $agency->created_at }}</td>
            </tr>

        </table>

        <a href="{{ route('agency.index') }}"
           class="btn btn-primary">
            Back To List
        </a>

    </div>
</div>

@endsection