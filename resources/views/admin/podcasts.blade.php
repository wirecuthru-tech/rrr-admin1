@extends('admin.layout')
@section('page-title','Podcasts')
@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card mb-3"><div class="card-body">
<form method="POST" action="{{ route('admin.live.store', 'podcasts') }}">@csrf
<div class="row"><div class="col-md-3 mb-2"><input class="form-control" name="title" placeholder="Title"></div>
<div class="col-md-3 mb-2"><input class="form-control" name="audio_url" placeholder="Audio Url"></div>
<div class="col-md-3 mb-2"><input class="form-control" name="description" placeholder="Description"></div>
<div class="col-md-3 mb-2"><input class="form-control" name="status" placeholder="Status"></div>
<div class="col-md-2 mb-2"><button class="btn btn-primary w-100">Add</button></div></div>
</form></div></div>
<div class="card"><div class="card-body table-responsive"><table class="table table-bordered table-hover"><thead class="table-dark"><tr><th>Title</th><th>Audio Url</th><th>Description</th><th>Status</th><th>Action</th></tr></thead><tbody>
@foreach($podcasts as $row)<tr><td>{{ $row['title'] ?? '' }}</td><td>{{ $row['audio_url'] ?? '' }}</td><td>{{ $row['description'] ?? '' }}</td><td>{{ $row['status'] ?? '' }}</td><td><form method="POST" action="{{ route('admin.live.delete', ['podcasts', $row['_id'] ?? '0']) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Delete</button></form></td></tr>@endforeach
</tbody></table></div></div>
@endsection
