@extends('admin.layout')
@section('page-title','Agencies')
@section('content')
@include('admin.partials.live-table', [
 'title'=>'Agency', 'collection'=>'agencies', 'items'=>$agencies,
 'fields'=>['agency_name'=>'Agency Name','owner_name'=>'Owner Name','mobile'=>'Mobile','email'=>'Email','commission'=>'Commission','country'=>'Country'],
 'columns'=>['agency_name'=>'Agency','owner_name'=>'Owner','mobile'=>'Mobile','email'=>'Email','commission'=>'Commission','country'=>'Country']
])
@endsection
