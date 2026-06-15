@extends('admin.layout')

@section('page-title','Agora Settings')

@section('content')

<div class="card"><div class="card-body">
<form method="POST" action="{{ route('admin.settings.update') }}">@csrf
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Agora App ID</label><input name="agora_app_id" class="form-control" value="{{ $settings['agora_app_id'] ?? '' }}"></div>
<div class="col-md-6"><label class="form-label">Agora App Certificate</label><input name="agora_app_certificate" class="form-control" value="{{ $settings['agora_app_certificate'] ?? '' }}"></div>
<div class="col-md-4"><label class="form-label">Token Expiry Seconds</label><input name="agora_token_expiry" type="number" class="form-control" value="{{ $settings['agora_token_expiry'] ?? 3600 }}"></div>
<div class="col-md-4"><label class="form-label">Voice Rooms</label><select name="agora_voice_enabled" class="form-control"><option value="1">Enable</option><option value="0">Disable</option></select></div>
<div class="col-md-4"><label class="form-label">Video Calls</label><select name="agora_video_enabled" class="form-control"><option value="1">Enable</option><option value="0">Disable</option></select></div>
</div><button class="btn btn-primary mt-4">Save Agora Settings</button>
</form>
<p class="text-muted mt-3 mb-0">App Certificate sirf admin/backend me rahega. Flutter app token API se join karega.</p>
</div></div>

@endsection
