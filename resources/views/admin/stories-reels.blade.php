@extends('admin.layout')
@section('page-title','Stories & Voice Reels')
@section('content')
<div class='row'><div class='col-md-6'><div class='card'><div class='card-body'><h5>Stories</h5><pre>{{ json_encode($stories, JSON_PRETTY_PRINT) }}</pre></div></div></div><div class='col-md-6'><div class='card'><div class='card-body'><h5>Voice Reels</h5><pre>{{ json_encode($reels, JSON_PRETTY_PRINT) }}</pre></div></div></div></div>
@endsection
