@extends('admin.layout')
@section('page-title','Create Agency')
@section('content')
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('agency.store') }}" class="row g-3">
@csrf
@foreach(['agency_name'=>'Agency Name','owner_name'=>'Owner Name','mobile'=>'Mobile','email'=>'Email','password'=>'Password','commission'=>'Commission','country'=>'Country'] as $name=>$label)
<div class="col-md-4"><label class="form-label">{{ $label }}</label><input class="form-control" name="{{ $name }}" type="{{ $name=='password'?'password':'text' }}"></div>
@endforeach
<div class="col-md-4"><label class="form-label">Status</label><select class="form-select" name="status"><option>Active</option><option>Inactive</option></select></div>
<div class="col-12"><button class="btn btn-primary">Save</button></div>
</form>
</div></div>
@endsection
