@extends('admin.layout')

@section('page-title','Add '.$roleName)

@section('content')

<div class="container-fluid">
    <h3>Add {{ $roleName }}</h3>

    <div class="card">
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.team.store', $role) }}">
                @csrf

                <div class="mb-3">
                    <label>User Real ID</label>
                    <input type="number" name="real_id" value="{{ old('real_id') }}" class="form-control" required placeholder="6 digit app user ID">
                </div>

                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Country</label>
                    <input type="text" name="country" value="{{ old('country') }}" class="form-control">
                </div>

                @if($parents->count() > 0)
                    <div class="mb-3">
                        <label>Parent Post</label>
                        <select name="parent_post_id" class="form-control" required>
                            <option value="">Select Parent</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->post_id }}">
                                    {{ $parent->badge_icon }} {{ $parent->name }} - {{ $parent->post_id }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <hr>
                <h5>Permissions</h5>

                <div class="row">
                    @foreach(['add','view','edit','delete','suspend','withdrawal'] as $perm)
                        <div class="col-md-4 mb-2">
                            <label>
                                <input type="checkbox" name="permission_{{ $perm }}" {{ in_array($perm, ['add','view']) ? 'checked' : '' }}>
                                {{ ucfirst($perm) }}
                            </label>
                        </div>
                    @endforeach
                </div>

                <button class="btn btn-success mt-3">Save</button>
            </form>

        </div>
    </div>
</div>

@endsection