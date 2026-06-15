@extends('admin.layout')

@section('page-title','Events')

@section('content')

<div class="card mb-3"><div class="card-body"><form method="POST" action="{{ route('admin.live.store','events') }}">@csrf<div class="row g-2"><div class="col-md-3"><input name="title" class="form-control" placeholder="Event title"></div><div class="col-md-2"><input name="type" class="form-control" placeholder="Type"></div><div class="col-md-2"><input name="entry_fee" type="number" class="form-control" placeholder="Entry fee"></div><div class="col-md-3"><input name="start_time" class="form-control" placeholder="Start time"></div><div class="col-md-2"><button class="btn btn-primary w-100">Add</button></div></div></form></div></div>
@include('admin.partials.live-table',['title'=>'Events','collection'=>'events','items'=>$events])

@endsection
