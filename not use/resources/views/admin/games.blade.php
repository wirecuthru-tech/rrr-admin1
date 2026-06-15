@extends('admin.layout')

@section('page-title','Game Management')

@section('content')

<div class="card">
<div class="card-body">

<table class="table">

<tr>
<th>Game ID</th>
<th>Game Name</th>
<th>Status</th>
</tr>

<tr>
<td>GM1001</td>
<td>Ludo</td>
<td>Active</td>
</tr>

<tr>
<td>GM1002</td>
<td>Teen Patti</td>
<td>Inactive</td>
</tr>

<tr>
<td>GM1003</td>
<td>Spin Wheel</td>
<td>Active</td>
</tr>

</table>

</div>
</div>

@endsection