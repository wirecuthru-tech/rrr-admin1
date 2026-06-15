@extends('admin.layout')
@section('page-title', 'Notification Management')
@section('content')
@include('admin.partials.live-table', [
 'title'=>'Notification', 'collection'=>'notifications', 'items'=>$notifications,
 'fields'=>['title'=>'Title','message'=>'Message','audience'=>'Audience'],
 'columns'=>['title'=>'Title','message'=>'Message','audience'=>'Audience']
])
@endsection
