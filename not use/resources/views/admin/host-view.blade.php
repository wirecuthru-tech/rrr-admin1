@extends('admin.layout')

@section('page-title','Host Profile')

@section('content')

<div class="row">

    <!-- Profile Card -->

    <div class="col-md-4">

        <div class="card">

            <div class="card-body text-center">

                <img src="https://via.placeholder.com/150"
                     class="rounded-circle mb-3"
                     width="150">

                <h3>Priya Sharma</h3>

                <p class="text-muted">
                    Host ID : H5001
                </p>

                <span class="badge bg-success">
                    Active Host
                </span>

                <br><br>

                <span class="badge bg-warning">
                    VIP 4
                </span>

            </div>

        </div>

    </div>

    <!-- Details -->

    <div class="col-md-8">

        <div class="card">

            <div class="card-body">

                <h4 class="mb-4">
                    Host Information
                </h4>

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <strong>User ID</strong><br>
                        512345
                    </div>

                    <div class="col-md-4 mb-3">
                        <strong>Agency ID</strong><br>
                        AG1001
                    </div>

                    <div class="col-md-4 mb-3">
                        <strong>Agency Name</strong><br>
                        Royal Agency
                    </div>

                    <div class="col-md-4 mb-3">
                        <strong>Name</strong><br>
                        Priya Sharma
                    </div>

                    <div class="col-md-4 mb-3">
                        <strong>Gender</strong><br>
                        Female
                    </div>

                    <div class="col-md-4 mb-3">
                        <strong>Country</strong><br>
                        India
                    </div>

                    <div class="col-md-4 mb-3">
                        <strong>Phone</strong><br>
                        +91 9876543210
                    </div>

                    <div class="col-md-4 mb-3">
                        <strong>Email</strong><br>
                        priya@gmail.com
                    </div>

                    <div class="col-md-4 mb-3">
                        <strong>Level</strong><br>
                        25
                    </div>

                    <div class="col-md-4 mb-3">
                        <strong>Followers</strong><br>
                        5420
                    </div>

                    <div class="col-md-4 mb-3">
                        <strong>Fans</strong><br>
                        3200
                    </div>

                    <div class="col-md-4 mb-3">
                        <strong>Status</strong><br>
                        Active
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- Earnings -->

<div class="row mt-4">

    <div class="col-md-3">

        <div class="card text-center">

            <div class="card-body">

                <h6>Coins</h6>

                <h2>250K</h2>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card text-center">

            <div class="card-body">

                <h6>Diamonds</h6>

                <h2>85K</h2>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card text-center">

            <div class="card-body">

                <h6>Monthly Earning</h6>

                <h2>?35K</h2>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card text-center">

            <div class="card-body">

                <h6>Total Earning</h6>

                <h2>?2.5L</h2>

            </div>

        </div>

    </div>

</div>

<!-- Room Stats -->

<div class="card mt-4">

    <div class="card-header">
        Room Statistics
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-3">
                <strong>Total Rooms</strong><br>
                25
            </div>

            <div class="col-md-3">
                <strong>Total Hours</strong><br>
                450
            </div>

            <div class="col-md-3">
                <strong>Room Followers</strong><br>
                2500
            </div>

            <div class="col-md-3">
                <strong>Peak Online</strong><br>
                500
            </div>

        </div>

    </div>

</div>

<!-- Withdraw History -->

<div class="card mt-4">

    <div class="card-header">
        Withdraw History
    </div>

    <div class="card-body">

        <table class="table table-bordered">

            <thead>

            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>

            </thead>

            <tbody>

            <tr>

                <td>WD1001</td>

                <td>01-06-2026</td>

                <td>?10,000</td>

                <td>
                    <span class="badge bg-success">
                        Paid
                    </span>
                </td>

            </tr>

            <tr>

                <td>WD1002</td>

                <td>15-06-2026</td>

                <td>?15,000</td>

                <td>
                    <span class="badge bg-warning">
                        Pending
                    </span>
                </td>

            </tr>

            </tbody>

        </table>

    </div>

</div>

<!-- Actions -->

<div class="mt-4">

    <button class="btn btn-primary">
        Edit Host
    </button>

    <button class="btn btn-success">
        Approve Host
    </button>

    <button class="btn btn-warning">
        Suspend Host
    </button>

    <button class="btn btn-danger">
        Remove Host
    </button>

</div>

@endsection