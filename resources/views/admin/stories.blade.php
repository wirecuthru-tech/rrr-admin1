@extends('admin.layout')
@section('page-title','Stories')
@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card mb-3"><div class="card-body">
<form method="POST" action="{{ route('admin.live.store', 'stories') }}">@csrf
<div class="row"><div class="col-md-3 mb-2"><input class="form-control" name="type" placeholder="Type"></div>
<div class="col-md-3 mb-2"><input class="form-control" name="media_url" placeholder="Media Url"></div>
<div class="col-md-3 mb-2"><input class="form-control" name="text" placeholder="Text"></div>
<div class="col-md-3 mb-2"><input class="form-control" name="status" placeholder="Status"></div>
<div class="col-md-2 mb-2"><button class="btn btn-primary w-100">Add</button></div></div>
</form></div></div>
<div class="card"><div class="card-body table-responsive"><table class="table table-bordered table-hover"><thead class="table-dark"><tr><th>Type</th><th>Media Url</th><th>Text</th><th>Status</th><th>Action</th></tr></thead><tbody>
@foreach($stories as $row)<tr><td>{{ $row['type'] ?? '' }}</td><td>{{ $row['media_url'] ?? '' }}</td><td>{{ $row['text'] ?? '' }}</td><td>{{ $row['status'] ?? '' }}</td><td><form method="POST" action="{{ route('admin.live.delete', ['stories', $row['_id'] ?? '0']) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Delete</button></form></td></tr>@endforeach
</tbody></table></div></div>
@endsection
