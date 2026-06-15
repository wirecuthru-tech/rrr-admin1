@extends('admin.layout')

@section('page-title','Host Withdraws')

@section('content')

<div class="card">
<div class="card-body">

<table class="table">

<tr>
<th>Withdraw ID</th>
<th>Host</th>
<th>Amount</th>
<th>Date</th>
<th>Status</th>
</tr>

<tr>
<td>WD1001</td>
<td>Priya</td>
<td>?10,000</td>
<td>01-06-2026</td>
<td>
<span class="badge bg-success">
Paid
</span>
</td>
</tr>

</table>

</div>
</div>

@endsection