@extends('admin.layout')
@section('page-title','AI Center')
@section('content')
<div class="row g-3">
  <div class="col-lg-5">
    <div class="card"><div class="card-body">
      <h5 class="mb-3">Free AI Settings</h5>
      <form method="POST" action="{{ route('admin.ai.center.update') }}">
        @csrf
        @php $s = $settings ?? []; @endphp
        <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" name="ai_moderator_enabled" {{ !empty($s['ai_moderator_enabled']) ? 'checked' : '' }}><label class="form-check-label">AI Moderator</label></div>
        <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" name="ai_subtitle_translation_enabled" {{ !empty($s['ai_subtitle_translation_enabled']) ? 'checked' : '' }}><label class="form-check-label">Free Subtitle Translation</label></div>
        <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" name="ai_matchmaking_enabled" {{ !empty($s['ai_matchmaking_enabled']) ? 'checked' : '' }}><label class="form-check-label">AI Matchmaking</label></div>
        <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" name="ai_event_host_enabled" {{ !empty($s['ai_event_host_enabled']) ? 'checked' : '' }}><label class="form-check-label">AI Event Host</label></div>
        <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="ai_recommendations_enabled" {{ !empty($s['ai_recommendations_enabled']) ? 'checked' : '' }}><label class="form-check-label">AI Recommendations</label></div>
        <label class="form-label">Default Language</label>
        <select name="ai_default_language" class="form-select mb-3">
          @foreach(['hi'=>'Hindi','en'=>'English','ar'=>'Arabic','ur'=>'Urdu','bn'=>'Bengali'] as $k=>$v)
            <option value="{{ $k }}" {{ ($s['ai_default_language'] ?? 'hi') === $k ? 'selected' : '' }}>{{ $v }}</option>
          @endforeach
        </select>
        <label class="form-label">Extra Blocked Words</label>
        <textarea name="ai_blocked_words" class="form-control mb-3" rows="4" placeholder="comma separated">{{ $s['ai_blocked_words'] ?? '' }}</textarea>
        <button class="btn btn-primary w-100">Save AI Settings</button>
      </form>
    </div></div>
  </div>
  <div class="col-lg-7">
    <div class="card mb-3"><div class="card-body">
      <h5>Production Note</h5>
      <p class="mb-0">Ye free AI layer paid API ke bina rule-based moderation, dictionary subtitle translation, scoring matchmaking, scripted event host aur ranked recommendations use karta hai. Future me OpenAI/Google/Azure provider yahin replace kar sakte ho.</p>
    </div></div>
    <div class="card"><div class="card-body table-responsive">
      <h5>Recent AI Moderation Logs</h5>
      <table class="table table-sm"><thead><tr><th>User</th><th>Severity</th><th>Action</th><th>Text</th></tr></thead><tbody>
        @forelse($moderationLogs as $log)
          <tr><td>{{ $log['user_id'] ?? '-' }}</td><td>{{ $log['severity'] ?? '-' }}</td><td>{{ $log['action'] ?? '-' }}</td><td>{{ Str::limit($log['text'] ?? '', 60) }}</td></tr>
        @empty <tr><td colspan="4">No logs</td></tr> @endforelse
      </tbody></table>
    </div></div>
  </div>
</div>
@endsection
