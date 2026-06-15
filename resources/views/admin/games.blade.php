@extends('admin.layout')
@section('page-title','Game Management')
@section('content')
@include('admin.partials.live-table', [
 'title'=>'Game', 'collection'=>'games', 'items'=>$games,
 'fields'=>['game_id'=>'Game ID','name'=>'Game Name'],
 'columns'=>['game_id'=>'Game ID','name'=>'Game Name']
])
@endsection
