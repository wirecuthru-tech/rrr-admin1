@extends('admin.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h3>Withdraw Requests</h3>
        <p class="text-muted">Manage user withdrawal requests</p>
    </div>

</div>

<div class="card shadow-sm border-0">

    <div class="card-body">

        <table class="table table-hover">

            <thead>

                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Account</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

            </thead>

            <tbody>

                <tr>
                    <td>512345</td>
                    <td>Rahul Sharma</td>
                    <td>?1500</td>
                    <td>UPI</td>
                    <td>rahul@paytm</td>
                    <td>04-06-2026</td>

                    <td>
                        <span class="badge bg-warning">
                            Pending
                        </span>
                    </td>

                    <td>
                        <button class="btn btn-success btn-sm">
                            Approve
                        </button>

                        <button class="btn btn-danger btn-sm">
                            Reject
                        </button>
                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>

@endsection