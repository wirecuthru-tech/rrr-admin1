@extends('admin.layout')

@section('page-title', 'Add New Gift')

@section('content')

<h3 class="mb-4">Add New Gift</h3>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.gift.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label>Gift Name</label>
                <input type="text" name="gift_name" class="form-control" required>
                @error('gift_name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label>Gift Price (Coins)</label>
                <input type="number" name="gift_price" class="form-control" required min="1">
                @error('gift_price') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label>Gift Category</label>
                <select name="gift_category" class="form-control" required>
                    <option value="">Select Category</option>
                    <option value="Normal">Normal</option>
                    <option value="VIP">VIP</option>
                    <option value="Event">Event</option>
                </select>
                @error('gift_category') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label>Gift Image</label>
                <input type="file" name="gift_image" class="form-control" accept="image/*">
                @error('gift_image') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-success">
                Save Gift
            </button>
            <a href="{{ route('admin.gifts') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>

@endsection