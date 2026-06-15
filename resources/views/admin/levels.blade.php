@extends('admin.layout')
@section('page-title','Levels')
@section('content')
@include('admin.partials.live-table', [
 'title'=>'Level', 'collection'=>'levels', 'items'=>$levels,
 'fields'=>['level'=>'Level','required_xp'=>'Required XP','reward'=>'Reward'],
 'columns'=>['level'=>'Level','required_xp'=>'Required XP','reward'=>'Reward']
])
@endsection
