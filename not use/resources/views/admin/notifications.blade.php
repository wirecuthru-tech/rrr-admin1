@extends('admin.layout')

@section('page-title', 'Notification Management')

@section('content')

<div class="card">
    <div class="card-body">

        <h3 class="mb-4">Send Notification</h3>

        <form>

            <div class="mb-3">
                <label>Send To</label>
                <select class="form-control">
                    <option>All Users</option>
                    <option>All Hosts</option>
                    <option>All Agencies</option>
                    <option>Specific User</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Notification Title</label>
                <input type="text" class="form-control">
            </div>

            <div class="mb-3">
                <label>Notification Message</label>
                <textarea class="form-control" rows="5"></textarea>
            </div>

            <button class="btn btn-primary">
                Send Notification
            </button>

        </form>

    </div>
</div>

<br>

<div class="card">
    <div class="card-body">

        <h4>Notification History</h4>

        <table class="table table-bordered">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Audience</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td>NT1001</td>
                    <td>Summer Event</td>
                    <td>All Users</td>
                    <td>04-06-2026</td>
                    <td>
                        <span class="badge bg-success">
                            Sent
                        </span>
                    </td>
                </tr>

            </tbody>

        </table>

    </div>
</div>

@endsection