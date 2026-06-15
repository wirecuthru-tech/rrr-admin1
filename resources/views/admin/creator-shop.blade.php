@extends('admin.layout')
@section('page-title','Creator Shop')
@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card mb-3"><div class="card-body">
<form method="POST" action="{{ route('admin.live.store', 'creator_shop_items') }}">@csrf
<div class="row"><div class="col-md-3 mb-2"><input class="form-control" name="host_id" placeholder="Host Id"></div>
<div class="col-md-3 mb-2"><input class="form-control" name="title" placeholder="Title"></div>
<div class="col-md-3 mb-2"><input class="form-control" name="type" placeholder="Type"></div>
<div class="col-md-3 mb-2"><input class="form-control" name="price" placeholder="Price"></div>
<div class="col-md-3 mb-2"><input class="form-control" name="status" placeholder="Status"></div>
<div class="col-md-2 mb-2"><button class="btn btn-primary w-100">Add</button></div></div>
</form></div></div>
<div class="card"><div class="card-body table-responsive"><table class="table table-bordered table-hover"><thead class="table-dark"><tr><th>Host Id</th><th>Title</th><th>Type</th><th>Price</th><th>Status</th><th>Action</th></tr></thead><tbody>
@foreach($items as $row)<tr><td>{{ $row['host_id'] ?? '' }}</td><td>{{ $row['title'] ?? '' }}</td><td>{{ $row['type'] ?? '' }}</td><td>{{ $row['price'] ?? '' }}</td><td>{{ $row['status'] ?? '' }}</td><td><form method="POST" action="{{ route('admin.live.delete', ['creator_shop_items', $row['_id'] ?? '0']) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Delete</button></form></td></tr>@endforeach
</tbody></table></div></div>
@endsection
