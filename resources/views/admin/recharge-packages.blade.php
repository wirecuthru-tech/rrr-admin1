@extends('admin.layout')
@section('page-title','Recharge Packages')
@section('content')
@include('admin.partials.live-table', [
 'title'=>'Recharge Package', 'collection'=>'recharge_packages', 'items'=>$packages,
 'fields'=>['package_id'=>'Package ID','amount'=>'Amount','coins'=>'Coins','bonus'=>'Bonus'],
 'columns'=>['package_id'=>'Package ID','amount'=>'Amount','coins'=>'Coins','bonus'=>'Bonus']
])
@endsection
