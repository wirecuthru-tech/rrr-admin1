@extends('admin.layout')

@section('page-title','PK Battles')

@section('content')

<div class="card mb-3"><div class="card-body"><form method="POST" action="{{ route('admin.live.store','pk_battles') }}">@csrf<div class="row g-2"><div class="col-md-3"><input name="host_a" class="form-control" placeholder="Host A"></div><div class="col-md-3"><input name="host_b" class="form-control" placeholder="Host B"></div><div class="col-md-2"><input name="score_a" type="number" class="form-control" placeholder="Score A"></div><div class="col-md-2"><input name="score_b" type="number" class="form-control" placeholder="Score B"></div><div class="col-md-2"><button class="btn btn-primary w-100">Add</button></div></div></form></div></div>
@include('admin.partials.live-table',['title'=>'PK Battles','collection'=>'pk_battles','items'=>$battles])

@endsection
