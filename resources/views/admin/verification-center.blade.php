@extends('admin.layout')
@section('page-title','Verification Center')
@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card p-3"><h6>User Pending</h6><h2>{{ $pendingUsers }}</h2><a href="{{ route('admin.verifications.users',['status'=>'pending_review']) }}" class="btn btn-primary btn-sm">Review Users</a></div></div>
    <div class="col-md-4"><div class="card p-3"><h6>User Verified</h6><h2>{{ $approvedUsers }}</h2><a href="{{ route('admin.verifications.users',['status'=>'verified']) }}" class="btn btn-success btn-sm">View Verified</a></div></div>
    <div class="col-md-4"><div class="card p-3"><h6>User Rejected</h6><h2>{{ $rejectedUsers }}</h2><a href="{{ route('admin.verifications.users',['status'=>'rejected']) }}" class="btn btn-danger btn-sm">View Rejected</a></div></div>
</div>
<div class="row g-3">
    <div class="col-md-4"><div class="card p-3"><h6>Host Pending</h6><h2>{{ $pendingHosts }}</h2><a href="{{ route('admin.verifications.hosts',['status'=>'pending_review']) }}" class="btn btn-primary btn-sm">Review Hosts</a></div></div>
    <div class="col-md-4"><div class="card p-3"><h6>Host Approved</h6><h2>{{ $approvedHosts }}</h2><a href="{{ route('admin.verifications.hosts',['status'=>'approved']) }}" class="btn btn-success btn-sm">View Approved</a></div></div>
    <div class="col-md-4"><div class="card p-3"><h6>Host Rejected</h6><h2>{{ $rejectedHosts }}</h2><a href="{{ route('admin.verifications.hosts',['status'=>'rejected']) }}" class="btn btn-danger btn-sm">View Rejected</a></div></div>
</div>
<div class="alert alert-info mt-4">Launch mode: Flutter ML Kit face detection + manual admin review. AWS Rekognition Face Liveness can be connected later.</div>
@endsection
