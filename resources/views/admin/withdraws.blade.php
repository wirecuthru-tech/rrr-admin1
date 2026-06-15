@extends('admin.layout')
@section('page-title','Withdraws')
@section('content')
@include('admin.partials.live-table', [
 'title'=>'Withdraw', 'collection'=>'withdraws', 'items'=>$withdraws,
 'fields'=>['user_name'=>'User','amount'=>'Amount','method'=>'Method','account'=>'Account'],
 'columns'=>['user_name'=>'User','amount'=>'Amount','method'=>'Method','account'=>'Account']
])
@endsection
