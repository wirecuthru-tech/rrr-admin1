@extends('admin.layout')

@section('page-title','Payment Gateway Settings')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.payment.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold">Paytm QR / Manual Recharge</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Enable Paytm QR</label>
                            <select name="paytm_qr_enabled" class="form-control">
                                <option value="1" {{ ($settings['paytm_qr_enabled'] ?? 1) == 1 ? 'selected' : '' }}>Enabled</option>
                                <option value="0" {{ ($settings['paytm_qr_enabled'] ?? 1) == 0 ? 'selected' : '' }}>Disabled</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Paytm UPI ID</label>
                            <input type="text" name="paytm_upi_id" class="form-control" value="{{ $settings['paytm_upi_id'] ?? '' }}" placeholder="yourupi@paytm">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Paytm Merchant Name</label>
                            <input type="text" name="paytm_merchant_name" class="form-control" value="{{ $settings['paytm_merchant_name'] ?? 'RRR Voice Chat' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Paytm QR Image URL</label>
                            <input type="text" name="paytm_qr_url" class="form-control" value="{{ $settings['paytm_qr_url'] ?? '' }}" placeholder="https://.../paytm-qr.png">
                            <small class="text-muted">QR image ko public URL par upload karke yahan paste karein. User app me ye QR dikh jayega.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Manual Approval Required</label>
                            <select name="paytm_manual_approval" class="form-control">
                                <option value="1" {{ ($settings['paytm_manual_approval'] ?? 1) == 1 ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ ($settings['paytm_manual_approval'] ?? 1) == 0 ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold">Razorpay</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Enable Razorpay</label>
                            <select name="razorpay_enabled" class="form-control">
                                <option value="1" {{ ($settings['razorpay_enabled'] ?? 0) == 1 ? 'selected' : '' }}>Enabled</option>
                                <option value="0" {{ ($settings['razorpay_enabled'] ?? 0) == 0 ? 'selected' : '' }}>Disabled</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Razorpay Key ID</label>
                            <input type="text" name="razorpay_key_id" class="form-control" value="{{ $settings['razorpay_key_id'] ?? '' }}" placeholder="rzp_live_xxxxx">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Razorpay Key Secret</label>
                            <input type="password" name="razorpay_key_secret" class="form-control" value="{{ $settings['razorpay_key_secret'] ?? '' }}" placeholder="Keep secret">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Razorpay Webhook Secret</label>
                            <input type="password" name="razorpay_webhook_secret" class="form-control" value="{{ $settings['razorpay_webhook_secret'] ?? '' }}">
                        </div>
                        <div class="alert alert-info mb-0">
                            Backend order + webhook endpoints add ho gaye hain. Real keys baad me yahan save kar dena.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold">Recharge Conversion</div>
                    <div class="card-body row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Coins per ₹1</label>
                            <input type="number" name="coins_per_rupee" class="form-control" value="{{ $settings['coins_per_rupee'] ?? 10 }}" min="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Minimum Recharge ₹</label>
                            <input type="number" name="min_recharge_amount" class="form-control" value="{{ $settings['min_recharge_amount'] ?? 10 }}" min="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Payment Mode</label>
                            <select name="payment_mode" class="form-control">
                                <option value="test" {{ ($settings['payment_mode'] ?? 'test') == 'test' ? 'selected' : '' }}>Test</option>
                                <option value="live" {{ ($settings['payment_mode'] ?? 'test') == 'live' ? 'selected' : '' }}>Live</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary">Save Payment Settings</button>
                            <a href="{{ route('admin.recharge.requests') }}" class="btn btn-outline-secondary">Recharge Requests</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
