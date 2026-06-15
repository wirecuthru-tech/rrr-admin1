@extends('admin.layout')
@section('page-title','Banner Management')
@section('content')
@include('admin.partials.live-table', [
 'title'=>'Banner', 'collection'=>'banners', 'items'=>$banners,
 'fields'=>['title'=>'Title','image'=>'Image URL','link'=>'Link','start_date'=>'Start Date','end_date'=>'End Date'],
 'columns'=>['title'=>'Title','image'=>'Image','link'=>'Link','start_date'=>'Start','end_date'=>'End']
])
@endsection
