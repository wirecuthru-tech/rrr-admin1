@extends('admin.layout')
@section('page-title','Family Chat')
@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card mb-3"><div class="card-body">
<form method="POST" action="{{ route('admin.live.store', 'family_chat') }}">@csrf
<div class="row"><div class="col-md-3 mb-2"><input class="form-control" name="family_id" placeholder="Family Id"></div>
<div class="col-md-3 mb-2"><input class="form-control" name="user_id" placeholder="User Id"></div>
<div class="col-md-3 mb-2"><input class="form-control" name="message" placeholder="Message"></div>
<div class="col-md-3 mb-2"><input class="form-control" name="status" placeholder="Status"></div>
<div class="col-md-2 mb-2"><button class="btn btn-primary w-100">Add</button></div></div>
</form></div></div>
<div class="card"><div class="card-body table-responsive"><table class="table table-bordered table-hover"><thead class="table-dark"><tr><th>Family Id</th><th>User Id</th><th>Message</th><th>Status</th><th>Action</th></tr></thead><tbody>
@foreach($messages as $row)<tr><td>{{ $row['family_id'] ?? '' }}</td><td>{{ $row['user_id'] ?? '' }}</td><td>{{ $row['message'] ?? '' }}</td><td>{{ $row['status'] ?? '' }}</td><td><form method="POST" action="{{ route('admin.live.delete', ['family_chat', $row['_id'] ?? '0']) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Delete</button></form></td></tr>@endforeach
</tbody></table></div></div>
@endsection
