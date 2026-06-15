@extends('admin.layout')
@section('page-title','Host Verifications')
@section('content')
<div class="d-flex gap-2 mb-3">
@foreach(['pending_review'=>'Pending','approved'=>'Approved','rejected'=>'Rejected','all'=>'All'] as $key=>$label)
<a class="btn btn-sm {{ $status===$key ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.verifications.hosts',['status'=>$key]) }}">{{ $label }}</a>
@endforeach
</div>
<div class="card p-3 table-responsive">
<table class="table table-dark table-hover align-middle">
<thead><tr><th>Selfie</th><th>User</th><th>Status</th><th>Provider</th><th>Submitted</th><th>Message</th><th>Action</th></tr></thead>
<tbody>
@forelse($verifications as $v)
@php $id = $v['_id']['$oid'] ?? $v['_id'] ?? ''; @endphp
<tr>
<td>@if(!empty($v['selfie_url']))<a href="{{ $v['selfie_url'] }}" target="_blank"><img src="{{ $v['selfie_url'] }}" style="width:70px;height:70px;border-radius:12px;object-fit:cover"></a>@else - @endif</td>
<td>{{ $v['user_id'] ?? '-' }}</td>
<td><span class="badge bg-{{ ($v['status'] ?? '')==='approved' ? 'success' : (($v['status'] ?? '')==='rejected' ? 'danger' : 'warning') }}">{{ $v['status'] ?? 'pending_review' }}</span></td>
<td>{{ $v['provider'] ?? 'flutter_mlkit_basic_face_check' }}</td>
<td>{{ $v['submitted_at'] ?? '-' }}</td>
<td>{{ $v['message'] ?? '-' }}</td>
<td>
<form method="POST" action="{{ route('admin.verifications.hosts.review',$id) }}" class="d-flex gap-1 flex-wrap">@csrf
<input name="reason" class="form-control form-control-sm" placeholder="Reason" style="width:130px">
<button name="status" value="approved" class="btn btn-success btn-sm">Approve</button>
<button name="status" value="rejected" class="btn btn-danger btn-sm">Reject</button>
</form>
</td>
</tr>
@empty
<tr><td colspan="7" class="text-center text-muted py-4">No host verification requests.</td></tr>
@endforelse
</tbody>
</table>
</div>
@endsection
