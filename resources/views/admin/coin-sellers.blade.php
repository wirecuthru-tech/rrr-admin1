@extends('admin.layout')
@section('title','Coin Seller Center')
@section('content')
<div class="container-fluid">
  <h3>Coin Seller Center</h3>
  @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
  @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
  <div class="card mb-3"><div class="card-body">
    <h5>Add / Activate Coin Seller</h5>
    <form method="POST" action="{{ route('admin.coin-sellers.store') }}" class="row g-2">@csrf
      <div class="col-md-2"><input class="form-control" name="real_id" placeholder="Real ID 555555" required></div>
      <div class="col-md-2"><input class="form-control" name="mobile" placeholder="Mobile" required></div>
      <div class="col-md-2"><input class="form-control" name="whatsapp" placeholder="WhatsApp"></div>
      <div class="col-md-2"><select class="form-control" name="seller_type"><option value="normal">Normal</option><option value="medium">Medium</option><option value="super">Super</option></select></div>
      <div class="col-md-2"><input class="form-control" name="seller_name" placeholder="Seller Name"></div>
      <div class="col-md-1"><input class="form-control" name="initial_coins" placeholder="Coins" type="number" min="0"></div>
      <div class="col-md-1"><button class="btn btn-primary w-100">Add</button></div>
      <div class="col-md-12 mt-2"><input class="form-control" name="bio" placeholder="Bio shown for Medium/Super seller notifications"></div>
    </form>
  </div></div>
  <div class="card"><div class="card-body table-responsive">
    <table class="table table-bordered align-middle"><thead><tr><th>Real ID</th><th>Name</th><th>Type</th><th>Mobile</th><th>WhatsApp</th><th>Coins</th><th>Status</th><th>Actions</th></tr></thead><tbody>
      @foreach($sellers as $s)
      <tr>
        <td>{{ $s['real_id'] ?? '' }}</td><td>{{ $s['seller_name'] ?? '' }}</td><td>{{ strtoupper($s['seller_type'] ?? '') }}</td><td>{{ $s['mobile'] ?? '' }}</td><td>{{ $s['whatsapp'] ?? '' }}</td><td>{{ $s['coin_balance'] ?? 0 }}</td><td>{{ $s['status'] ?? '' }}</td>
        <td>
          <form method="POST" action="{{ route('admin.coin-sellers.coins', $s['real_id'] ?? '') }}" class="d-flex gap-1 mb-1">@csrf
            <select name="action" class="form-control"><option value="add">Add</option><option value="deduct">Deduct</option></select><input name="coins" type="number" min="1" class="form-control" placeholder="Coins"><button class="btn btn-sm btn-success">Save</button>
          </form>
          <form method="POST" action="{{ route('admin.coin-sellers.status', $s['real_id'] ?? '') }}" class="d-flex gap-1">@csrf
            <select name="status" class="form-control"><option value="active">Active</option><option value="deactive">Deactive</option><option value="inactive">Inactive</option></select><button class="btn btn-sm btn-warning">Update</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody></table>
  </div></div>
</div>
@endsection
