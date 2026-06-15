@extends('admin.layout')

@section('page-title', 'Banner Management')

@section('content')

<div class="row">

    <!-- Add Banner -->

    <div class="col-md-4">

        <div class="card">

            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fa-solid fa-image"></i>
                    Add Banner
                </h5>
            </div>

            <div class="card-body">

                <form>

                    <div class="mb-3">
                        <label class="form-label">
                            Banner Title
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            placeholder="Enter banner title">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Banner Image
                        </label>

                        <input
                            type="file"
                            class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Banner Link
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            placeholder="https://">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Start Date
                        </label>

                        <input
                            type="date"
                            class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            End Date
                        </label>

                        <input
                            type="date"
                            class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Status
                        </label>

                        <select class="form-select">
                            <option>Active</option>
                            <option>Inactive</option>
                        </select>
                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary w-100">

                        <i class="fa-solid fa-plus"></i>
                        Add Banner

                    </button>

                </form>

            </div>

        </div>

    </div>

    <!-- Banner List -->

    <div class="col-md-8">

        <div class="card">

            <div class="card-header">
                <h5 class="mb-0">
                    Banner List
                </h5>
            </div>

            <div class="card-body">

                <table class="table table-hover align-middle">

                    <thead>

                    <tr>
                        <th>ID</th>
                        <th>Banner</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Action</th>
                    </tr>

                    </thead>

                    <tbody>

                    <tr>

                        <td>BN1001</td>

                        <td>
                            <img
                                src="https://via.placeholder.com/120x60"
                                width="120">
                        </td>

                        <td>
                            Summer Festival
                        </td>

                        <td>
                            <span class="badge bg-success">
                                Active
                            </span>
                        </td>

                        <td>
                            01-06-2026
                        </td>

                        <td>
                            30-06-2026
                        </td>

                        <td>

                            <button
                                class="btn btn-warning btn-sm">
                                Edit
                            </button>

                            <button
                                class="btn btn-danger btn-sm">
                                Delete
                            </button>

                        </td>

                    </tr>

                    <tr>

                        <td>BN1002</td>

                        <td>
                            <img
                                src="https://via.placeholder.com/120x60"
                                width="120">
                        </td>

                        <td>
                            VIP Recharge Offer
                        </td>

                        <td>
                            <span class="badge bg-secondary">
                                Inactive
                            </span>
                        </td>

                        <td>
                            10-06-2026
                        </td>

                        <td>
                            20-06-2026
                        </td>

                        <td>

                            <button
                                class="btn btn-warning btn-sm">
                                Edit
                            </button>

                            <button
                                class="btn btn-danger btn-sm">
                                Delete
                            </button>

                        </td>

                    </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection