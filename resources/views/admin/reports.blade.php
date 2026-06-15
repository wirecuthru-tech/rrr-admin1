@extends('admin.layout')
@section('page-title','Reports Management')
@section('content')
@include('admin.partials.live-table', [
 'title'=>'Report', 'collection'=>'reports', 'items'=>$reports,
 'fields'=>['report_id'=>'Report ID','user_name'=>'User','reason'=>'Reason','type'=>'Type'],
 'columns'=>['report_id'=>'Report ID','user_name'=>'User','reason'=>'Reason','type'=>'Type']
])
@endsection
