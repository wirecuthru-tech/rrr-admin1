@extends('admin.layout')

@section('page-title','Families / Kingdom')

@section('content')

<div class="card mb-3"><div class="card-body"><form method="POST" action="{{ route('admin.live.store','families') }}">@csrf<div class="row g-2"><div class="col-md-3"><input name="name" class="form-control" placeholder="Family name"></div><div class="col-md-3"><input name="country" class="form-control" placeholder="Country"></div><div class="col-md-2"><input name="level" type="number" class="form-control" placeholder="Level"></div><div class="col-md-2"><input name="coins" type="number" class="form-control" placeholder="Coins"></div><div class="col-md-2"><button class="btn btn-primary w-100">Add</button></div></div></form></div></div>
@include('admin.partials.live-table',['title'=>'Families','collection'=>'families','items'=>$families])

@endsection
