@extends('admin.layout')

@section('page-title','Edit Agency')

@section('content')

<div class="card">
    <div class="card-body">

        <h3 class="mb-4">Edit Agency</h3>

        <form method="POST"
              action="{{ route('agency.update',$agency->_id) }}">
            @csrf

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label>Agency Name</label>
                    <input type="text"
                           name="agency_name"
                           class="form-control"
                           value="{{ $agency->agency_name }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Owner Name</label>
                    <input type="text"
                           name="owner_name"
                           class="form-control"
                           value="{{ $agency->owner_name }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Mobile Number</label>
                    <input type="text"
                           name="mobile"
                           class="form-control"
                           value="{{ $agency->mobile }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email"
                           name="email"
                           class="form-control"
                           value="{{ $agency->email }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Commission %</label>
                    <input type="number"
                           name="commission"
                           class="form-control"
                           value="{{ $agency->commission }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Country</label>
                    <input type="text"
                           name="country"
                           class="form-control"
                           value="{{ $agency->country }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Status</label>

                    <select name="status" class="form-control">

                        <option value="Active"
                            {{ $agency->status=='Active' ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="Inactive"
                            {{ $agency->status=='Inactive' ? 'selected' : '' }}>
                            Inactive
                        </option>

                    </select>
                </div>

            </div>

            <button type="submit" class="btn btn-success">
                Update Agency
            </button>

            <a href="{{ route('agency.index') }}"
               class="btn btn-secondary">
                Back
            </a>

        </form>

    </div>
</div>

@endsection