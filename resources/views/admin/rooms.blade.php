@extends('admin.layout')
@section('page-title', 'Rooms Management')
@section('content')
@include('admin.partials.live-table', [
 'title'=>'Room', 'collection'=>'rooms', 'items'=>$rooms,
 'fields'=>['room_id'=>'Room ID','name'=>'Room Name','owner_name'=>'Owner','user_count'=>'Users'],
 'columns'=>['room_id'=>'Room ID','name'=>'Room Name','owner_name'=>'Owner','user_count'=>'Users']
])
@endsection
