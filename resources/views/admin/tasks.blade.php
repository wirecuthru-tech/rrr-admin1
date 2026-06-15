@extends('admin.layout')
@section('page-title','Tasks')
@section('content')
@include('admin.partials.live-table', [
 'title'=>'Task', 'collection'=>'tasks', 'items'=>$tasks,
 'fields'=>['name'=>'Task Name','reward'=>'Reward','type'=>'Type'],
 'columns'=>['name'=>'Task','reward'=>'Reward','type'=>'Type']
])
@endsection
