@extends('admin.layout')

@section('page-title','VIP Plans')

@section('content')

<div class="card">
<div class="card-body">

<table class="table">

<tr>
<th>VIP</th>
<th>Price</th>
<th>Duration</th>
<th>Benefits</th>
</tr>

<tr>
<td>VIP 1</td>
<td>?99</td>
<td>30 Days</td>
<td>VIP Badge</td>
</tr>

<tr>
<td>VIP 2</td>
<td>?299</td>
<td>30 Days</td>
<td>Badge + Frame</td>
</tr>

<tr>
<td>VIP 3</td>
<td>?599</td>
<td>30 Days</td>
<td>All Features</td>
</tr>

</table>

</div>
</div>

@endsection