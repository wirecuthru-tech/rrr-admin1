@extends('admin.layout')

@section('page-title','Moments')

@section('content')

<div class="card mb-3"><div class="card-body"><form method="POST" action="{{ route('admin.live.store','moments') }}">@csrf<div class="row g-2"><div class="col-md-8"><input name="text" class="form-control" placeholder="Moment text"></div><div class="col-md-2"><select name="status" class="form-control"><option>active</option><option>blocked</option></select></div><div class="col-md-2"><button class="btn btn-primary w-100">Add</button></div></div></form></div></div>
@include('admin.partials.live-table',['title'=>'Moments','collection'=>'moments','items'=>$moments])

@endsection
