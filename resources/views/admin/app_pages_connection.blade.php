@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
  <h3>App Pages ↔ Laravel API Connection</h3>
  <p>All Flutter pages are connected to Laravel API/Admin Panel modules below.</p>
  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead><tr><th>Flutter Page</th><th>Admin Control</th><th>API Endpoints</th><th>Admin URL</th></tr></thead>
      <tbody>
      @foreach($pages as $p)
        <tr>
          <td>{{ $p['page'] }}</td>
          <td>{{ $p['admin'] }}</td>
          <td><code>{{ $p['api'] }}</code></td>
          <td><code>{{ $p['admin_url'] }}</code></td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
