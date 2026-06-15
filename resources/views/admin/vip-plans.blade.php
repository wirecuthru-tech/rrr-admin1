@extends('admin.layout')
@section('page-title','VIP Plans')
@section('content')
@include('admin.partials.live-table', [
 'title'=>'VIP Plan', 'collection'=>'vip_plans', 'items'=>$plans,
 'fields'=>['vip'=>'VIP Name','price'=>'Price','duration'=>'Duration','benefits'=>'Benefits'],
 'columns'=>['vip'=>'VIP','price'=>'Price','duration'=>'Duration','benefits'=>'Benefits']
])
@endsection
